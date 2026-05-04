<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buscador</title>
</head>
<body>

    <div class="col-lg-3 col-md-4 pd-left-none no-pd">
        <div class="filter-secs">
            <div class="filter-heading">
                <h3>Search</h3>
                <!-- <a href="#" id="clear-search">Clear all filters</a> -->
            </div>

            <div class="paddy">
                <div class="filter-dd">
                    <div class="filter-ttl">
                        <h3>User City or User Name:</h3>
                        <a href="#" id="clear-city">Clear</a>
                    </div>
                    <form id="formSearch">
                        <input type="text" id="search" placeholder="Nombre o Ciudad">
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="resultados"></div>

    <script>
        // Prevenir que el formulario se envíe al presionar Enter
        const form = document.getElementById("formSearch");
        if (form) {
            form.addEventListener('submit', event => {
                event.preventDefault();
                console.log("Formulario no enviado, página no recargada.");
            });
        }
    </script>

</body>
</html>