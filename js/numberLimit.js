// Validierungsfunktion, um negative Zahlen zu verhindern
    function watch(obj){
        console.log(obj.value);
        if (obj.value < 1) {
            obj.value = 1;
        }
    }