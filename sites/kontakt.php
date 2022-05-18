<?php
register_style("kontakt");
register_title("Kontakt");

function page_render()
{
?>
    <style>
        a#facebook-link {
            background-color: rgba(0, 0, 0, 0.35);
            color: #3c5a9a;
            width: fit-content;
            padding: .5em;
            margin: 1em auto;
        }

        a#facebook-link:hover {
            background-color: #3c5a9a;
            color: #ffffff;
        }
    </style>
    <div id="content">
        <h1> KONTAKT </h1>
        <p class="big spacious"> Wszelkie pytania, sugestie i propozycje należy kierować do prof. Jacka Burzyńskiego. </p>
        <p class="big spacious"> Można skontaktować się z nami również poprzez nasz oficjalny fanpage na facebook'u: </p>
        <div class="link big">
            <a id="facebook-link" target="_blank" href="https://www.facebook.com/Pi%C5%82ka-no%C5%BCna-VIII-LO-w-Katowicach-182879961837032/">
                facebook.com/PILKA-NOZNA-8-LO
            </a>
        </div>
    </div>
<?php }
