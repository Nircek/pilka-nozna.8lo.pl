window.onload = () => {
    var inp = document.getElementById('name');
    var inpid = document.getElementById('id');
    inp.oninput = () => {
        let match;
        if ((match = /^(\d{4,})(\/\d{4,})?$/.exec(inp.value)) !== null) {
            inp.value = match[1] + '/' + (+match[1] + 1);
            inpid.value = match[1];
        }
    }
}
