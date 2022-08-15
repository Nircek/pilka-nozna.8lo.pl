-- @init()
CREATE TABLE IF NOT EXISTS `prefix_article` (
    `article_id` int(10) UNSIGNED NOT NULL,
    `season_id` int(10) UNSIGNED DEFAULT NULL,
    `title` tinytext COLLATE utf8mb4_polish_ci NOT NULL,
    `author_id` int(10) UNSIGNED NOT NULL,
    `publish_on_news_page` tinyint(1) NOT NULL,
    `is_subpage` tinyint(1) NOT NULL,
    `content` mediumtext COLLATE utf8mb4_polish_ci NOT NULL,
    `created_at` date DEFAULT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_polish_ci;

-- @init()
CREATE TABLE IF NOT EXISTS `prefix_game` (
    `game_id` int(10) UNSIGNED NOT NULL,
    `season_id` int(10) UNSIGNED NOT NULL,
    `type` enum(
        'first',
        'second',
        'half1',
        'half2',
        'final',
        'third'
    ) COLLATE utf8mb4_polish_ci NOT NULL,
    `date` date NOT NULL,
    `A_team_id` int(10) DEFAULT NULL,
    `A_score` int(11) DEFAULT NULL,
    `B_score` int(11) DEFAULT NULL,
    `B_team_id` int(10) DEFAULT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_polish_ci;

-- @init()
CREATE TABLE IF NOT EXISTS `prefix_photo` (
    `photo_id` int(11) UNSIGNED NOT NULL,
    `season_id` int(10) UNSIGNED NOT NULL,
    `game_id` int(10) UNSIGNED DEFAULT NULL,
    `date` date NOT NULL,
    `type` enum('filename', 'url') COLLATE utf8mb4_polish_ci NOT NULL,
    `content` text COLLATE utf8mb4_polish_ci NOT NULL,
    `photographer_id` int(10) UNSIGNED NOT NULL,
    `credit_photographer` tinyint(1) NOT NULL DEFAULT '0',
    `comment` text COLLATE utf8mb4_polish_ci
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_polish_ci;

-- @init()
CREATE TABLE IF NOT EXISTS `prefix_season` (
    `season_id` int(10) UNSIGNED NOT NULL,
    `created_at` date NOT NULL,
    `name` tinytext COLLATE utf8mb4_polish_ci NOT NULL,
    `html_name` tinytext COLLATE utf8mb4_polish_ci NOT NULL,
    `description` text COLLATE utf8mb4_polish_ci,
    `grouping_type` enum('no_grouping', 'two_rounds', 'two_groups') COLLATE utf8mb4_polish_ci NOT NULL,
    `comment` text COLLATE utf8mb4_polish_ci
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_polish_ci;

-- @init()
CREATE TABLE IF NOT EXISTS `prefix_team` (
    `season_id` int(10) UNSIGNED NOT NULL,
    `team_id` int(10) NOT NULL,
    `name` varchar(42) COLLATE utf8mb4_polish_ci NOT NULL,
    `photo_id` int(10) UNSIGNED DEFAULT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_polish_ci;

-- @init()
CREATE TABLE IF NOT EXISTS `prefix_user` (
    `active` tinyint(1) NOT NULL,
    `user_id` int(10) UNSIGNED NOT NULL,
    `login` tinytext COLLATE utf8mb4_polish_ci NOT NULL,
    `password` varchar(255) COLLATE utf8mb4_polish_ci NOT NULL COMMENT 'plain if `change_password_at_next_login` else hash',
    `change_password_at_next_login` tinyint(1) NOT NULL,
    `pretty_name` varchar(255) COLLATE utf8mb4_polish_ci NOT NULL,
    `last_logged_at` date NOT NULL,
    `registered_at` date NOT NULL,
    `comment` text COLLATE utf8mb4_polish_ci,
    `admin` tinyint(1) NOT NULL,
    `photographer` tinyint(1) NOT NULL,
    `referee` tinyint(1) NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_polish_ci;

-- get_latest_season() # 1
SELECT
    `season_id`
FROM
    `prefix_season`
WHERE
    `grouping_type` <> 'no_grouping'
ORDER BY
    `created_at` DESC
LIMIT
    1;

-- get_seasons() # 2
SELECT
    `season_id`,
    `name`,
    `html_name`
FROM
    `prefix_season`
ORDER BY
    `created_at` DESC;

-- get_gallery_seasons() # 1
SELECT
    p.`season_id`,
    s.`name`,
    s.`html_name`
FROM
    `prefix_photo` p
    LEFT JOIN `prefix_season` s ON p.`season_id` = s.`season_id`
GROUP BY
    `season_id`
ORDER BY
    s.`created_at` DESC;

-- get_season(season) # 3
SELECT
    *
FROM
    `prefix_season`
WHERE
    `season_id` = ?;

-- get_games(season, finals?, group) # 8
SELECT
    g.`game_id`,
    a.`name` AS `A_team`,
    b.`name` AS `B_team`,
    g.`A_score`,
    g.`B_score`,
    CASE
        WHEN g.`date` IS NULL
        OR YEAR(g.`date`) = 0 THEN NULL
        ELSE g.`date`
    END AS `date`,
    g.`type`,
    IF(
        g.`type` IN ('first', 'second'),
        NULL,
        SUBSTRING_INDEX(
            SUBSTRING_INDEX(
                'PÓŁFINAŁ,FINAŁ,3 MIEJSCE',
                ',',
                FIND_IN_SET(g.`type`, 'final,third') + 1
            ),
            ',',
            -1
        )
    ) AS `title`
FROM
    `prefix_game` g
    LEFT JOIN `prefix_team` a ON g.`season_id` = a.`season_id`
    AND g.`A_team_id` = a.`team_id`
    LEFT JOIN `prefix_team` b ON g.`season_id` = b.`season_id`
    AND g.`B_team_id` = b.`team_id`
WHERE
    g.`season_id` = ?
    AND (
        (
            ?
            AND g.`type` NOT IN ('first', 'second')
        )
        OR g.`type` = ?
    )
ORDER BY
    g.`type`,
    `date`,
    `game_id`;

-- count_gallery_photos(season) # 1
SELECT
    COUNT(*)
FROM
    `prefix_photo`
WHERE
    `season_id` = ?;

-- get_season_name(season) # 5
SELECT
    `name`
FROM
    `prefix_season`
WHERE
    `season_id` = ?;

-- get_gallery_photos(PREFIX, season) # 1
SELECT
    `photo_id`,
    `game_id`,
    `date`,
    `type`,
    CONCAT(
        IF(
            `type` = 'filename',
            CONCAT(`PREFIX`, '/zdjecia/'),
            ''
        ),
        `content`
    ) AS `url`,
    CONCAT(
        IF(
            `type` = 'filename',
            CONCAT(`PREFIX`, '/zdjecia/thumb.'),
            ''
        ),
        `content`
    ) AS `thumb_url`,
    `photographer_id`,
    `credit_photographer`,
    `comment`
FROM
    `prefix_photo` p,
    (
        SELECT
            ? AS PREFIX
    ) P
WHERE
    `season_id` = ?
ORDER BY
    `date`,
    `photographer_id`,
    `photo_id`;

-- get_4_random_photos(PREFIX) # 1
SELECT
    CONCAT(
        IF(
            `type` = 'filename',
            CONCAT(?, '/zdjecia/thumb.'),
            ''
        ),
        `content`
    ) AS `thumb_url`
FROM
    `prefix_photo`
ORDER BY
    RAND()
LIMIT
    4;

-- get_group_table(season, all?, group) # 5
SELECT
    tt.team AS `id`,
    T.`name` AS team,
    3 * win + tie AS points,
    win,
    tie,
    los,
    our AS gain,
    their AS lost,
    CONCAT(IF((our - their) > 0, '+', ''), our - their) AS delta
FROM
    (
        SELECT
            `season_id`,
            us AS team,
            SUM(IF(our > their, 1, 0)) AS win,
            SUM(IF(our = their, 1, 0)) AS tie,
            SUM(IF(our < their, 1, 0)) AS los,
            IFNULL(SUM(our), 0) AS our,
            IFNULL(SUM(their), 0) AS their
        FROM
            (
                SELECT
                    `season_id`,
                    `type`,
                    `A_team_id` AS us,
                    `A_score` AS our,
                    `B_score` AS their
                FROM
                    `prefix_game`
                UNION
                ALL
                SELECT
                    `season_id`,
                    `type`,
                    `B_team_id` AS us,
                    `B_score` AS our,
                    `A_score` AS their
                FROM
                    `prefix_game`
            ) t
        WHERE
            `season_id` = ?
            AND `type` IN ('first', 'second')
            AND (
                ?
                XOR `type` = ?
            )
        GROUP BY
            `us`
    ) tt
    LEFT JOIN `prefix_team` T ON T.`season_id` = tt.`season_id`
    AND T.`team_id` = tt.team
ORDER BY
    points DESC,
    delta DESC,
    team ASC;

-- get_news() # 1
SELECT
    `article_id`,
    `title`,
    `content`,
    `created_at`
FROM
    `prefix_article`
WHERE
    `publish_on_news_page` = 1
ORDER BY
    `article_id` DESC;

-- get_recent_news() # 1
SELECT
    `article_id`,
    `title`,
    `content`,
    `created_at`
FROM
    `prefix_article`
WHERE
    `publish_on_news_page` = 1
ORDER BY
    `article_id` DESC
LIMIT
    6;

-- add_info(title, author, content) # 1
INSERT INTO
    `prefix_article` (
        `season_id`,
        `title`,
        `author_id`,
        `publish_on_news_page`,
        `is_subpage`,
        `content`,
        `created_at`
    )
SELECT
    `season_id`,
    ?,
    ?,
    1,
    0,
    ?,
    CURDATE()
FROM
    `prefix_season`
ORDER BY
    `created_at` DESC
LIMIT
    1;

-- add_photo(season, filename, photographer) # 1
INSERT INTO
    `prefix_photo` (
        `season_id`,
        `game_id`,
        `date`,
        `type`,
        `content`,
        `photographer_id`,
        `credit_photographer`,
        `comment`
    )
VALUES
    (?, NULL, CURDATE(), 'filename', ?, ?, 0, NULL);

-- get_game_ids(season) # 1
SELECT
    `game_id`
FROM
    `prefix_game`
WHERE
    `season_id` = ?;

-- get_game_types(season) # 1
SELECT
    `game_id`,
    `type`
FROM
    `prefix_game`
WHERE
    `season_id` = ?;

-- set_game_date(date, season, game_id) # 1
UPDATE
    `prefix_game`
SET
    `date` = ?
WHERE
    `season_id` = ?
    AND `game_id` = ?;

-- set_game_score(A, B, season, game_id) # 1
UPDATE
    `prefix_game`
SET
    `A_score` = ?,
    `B_score` = ?
WHERE
    `season_id` = ?
    AND `game_id` = ?;

-- update_final_participants(season) # 1
UPDATE
    `prefix_game` u,
    (
        SELECT
            IF(`wins`, 'final', 'third') AS type,
            SUM(
                CASE
                    WHEN `row` = 1 THEN `team`
                END
            ) AS A,
            SUM(
                CASE
                    WHEN `row` = 2 THEN `team`
                END
            ) AS B
        FROM
            (
                SELECT
                    SUBSTR(`type`, 5, 1) AS `row`,
                    f AS `wins`,
                    IF(
                        A_score - B_score,
                        IF(
                            f
                            XOR A_score < B_score,
                            A_team_id,
                            B_team_id
                        ),
                        NULL
                    ) AS `team`
                FROM
                    `prefix_game`,
                    (
                        SELECT
                            0 AS f
                        UNION
                        SELECT
                            1 AS f
                    ) f
                WHERE
                    `season_id` = ?
                    AND SUBSTR(`type`, 1, 4) = 'half'
            ) t
        GROUP BY
            `wins`
    ) x
SET
    u.`A_team_id` = x.A,
    u.`B_team_id` = x.B
WHERE
    u.`type` = x.type;

-- delete_finals(season) # 1
DELETE FROM
    `prefix_game`
WHERE
    `season_id` = ?
    AND `type` NOT IN ('first', 'second');

-- add_finals(season, f1, s1, f2, s2) # 1
INSERT INTO
    `prefix_game` (
        `game_id`,
        `season_id`,
        `type`,
        `A_team_id`,
        `B_team_id`
    )
SELECT
    maxi + i,
    id,
    `type`,
    A,
    B
FROM
    (
        SELECT
            max(`game_id`) AS maxi,
            id
        FROM
            `prefix_game` g,
            (
                SELECT
                    ? AS id
            ) i
        WHERE
            `season_id` = id
    ) m,
    (
        SELECT
            1 AS i,
            'half1' AS `type`,
            ? AS A,
            ? AS B
        UNION
        SELECT
            2 AS i,
            'half2' AS `type`,
            ? AS A,
            ? AS B
        UNION
        SELECT
            3 AS i,
            'third' AS `type`,
            NULL AS A,
            NULL AS B
        UNION
        SELECT
            4 AS i,
            'final' AS `type`,
            NULL AS A,
            NULL AS B
    ) t;

-- get_static_site(title)
SELECT
    `title`,
    `content`
FROM
    `prefix_article`
WHERE
    `season_id` IS NULL
    AND `is_subpage` = 1
    AND `title` REGEXP REPLACE(?, '-', '[- ]')
LIMIT
    1;

-- get_static_sites()
SELECT
    LOWER(REPLACE(`title`, ' ', '-')) AS `name`,
    UPPER(`title`) AS `title`
FROM
    `prefix_article`
WHERE
    `season_id` IS NULL
    AND `is_subpage` = 1
ORDER BY
    `article_id`;

-- add_season(id, name, html_name, grouping)
INSERT INTO
    `prefix_season` (
        `season_id`,
        `created_at`,
        `name`,
        `html_name`,
        `description`,
        `grouping_type`,
        `comment`
    )
VALUES
    (
        ?,
        CURRENT_DATE(),
        ?,
        ?,
        NULL,
        ?,
        NULL
    );

-- update_season(name, html_name, description, id)
UPDATE
    `prefix_season`
SET
    `name` = ?,
    `html_name` = ?,
    `description` = ?
WHERE
    `season_id` = ?;

-- {END}
