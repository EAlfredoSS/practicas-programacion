# Avisos rojos en `templates/header.php` (estado actual)

Este documento lista todos los avisos de color rojo (Bootstrap: `alert alert-danger`) que se muestran para usuarios nuevos o con perfil incompleto, e incluye el código PHP y las consultas SQL que determinan si se muestran o no. Nos centramos únicamente en los avisos rojos.

## Contexto y flujo de datos

- Punto de entrada: `user/me.php` requiere `templates/header.php`.
- `header.php` inicia sesión y carga los datos del usuario autenticado usando la clave de sesión `$_SESSION['orden2017']`.
- Consulta base (única consulta que alimenta los 3 avisos rojos):

```php
$identificador2017 = $_SESSION['orden2017'];
$query = "SELECT * FROM mentor2009 WHERE orden='" . $identificador2017 . "'";
$result = mysqli_query($link, $query);
$fila = mysqli_fetch_array($result);

// Campos utilizados por los avisos rojos
$email_verified = $fila['Emailverif'];
$gpslat11      = $fila['Gpslat'];
$gpslng11      = $fila['Gpslng'];
$zonaHoraria   = $fila['timeshift'];
```

Observaciones SQL:
- No se usan consultas preparadas; el `orden` de sesión se interpola directamente en la cadena SQL.
- Todos los avisos rojos dependen exclusivamente de los campos del `SELECT` anterior; no hay consultas adicionales para estos avisos.

---

## Aviso 1: Email no verificado

Se muestra si el campo `Emailverif` es falso/0.

Hay dos bloques equivalentes en el archivo (uno muy temprano y otro ya dentro del `<main>`); ambos muestran el aviso y detienen la ejecución.

1) Bloque temprano (antes del `<html>`; incluye cabecera simplificada y sale):

```php
if(!$email_verified)
{
    require('../templates/header_simplified.html');
    ?>
    <div class="alert alert-danger" align="center">
       To see all the contents you need to validate your email address. If you did not receive any email click <a style="text-decoration: underline;" href=<?php echo "./verify_email.php" ?> >here</a>. (Check also your Spam folder.)</br>
    </div>
    <?php
    exit(0);
}
```

2) Bloque dentro de `<main>` (mensaje equivalente y `exit(0)`):

```php
if (!$email_verified)
{
    ?>
    <div class="alert alert-danger" align="center">
       To see all the contents you need to validate your email address. If you did not receive any email click <a style="text-decoration: underline;" href=<?php echo "./verify_email.php" ?> >here</a>.</br>
    </div>
    <?php
    exit(0);
}
```

SQL implicada: la verificación depende de `Emailverif` de la consulta base:

```sql
SELECT * FROM mentor2009 WHERE orden = <$_SESSION['orden2017']>
```

---

## Aviso 2: Ubicación no establecida

Se muestra cuando no hay coordenadas guardadas (ambas a 0):

```php
if ($gpslat11 == 0 and $gpslng11 == 0) {
    ?>
    <div class="alert alert-danger" align="center">
        <strong>Important!</strong> Provide your location in order to continue. <strong><a href="./getgpsposition.php" style=" text-decoration: underline;"> Add location</a></strong>
    </div>
    <?php
}
```

SQL implicada: campos `Gpslat` y `Gpslng` de la misma consulta base al cargar el usuario.

```sql
SELECT * FROM mentor2009 WHERE orden = <$_SESSION['orden2017']>
```

---

## Aviso 3: Zona horaria no definida

Se muestra cuando la zona horaria tiene el valor por defecto usado como "no configurado" (`'Antarctica/Casey'`). Este aviso solo se evalúa si ya hay ubicación (el `else if` viene tras el check de GPS):

```php
else if($zonaHoraria=='Antarctica/Casey')
{
    ?>
    <div class="alert alert-danger" align="center">
        <strong>Important!</strong> Provide time zone in order to continue. <strong><a href="./timeshift.php" style=" text-decoration: underline;"> Add time zone</a></strong>
    </div>
    <?php
}
```

SQL implicada: campo `timeshift` de la misma consulta base.

```sql
SELECT * FROM mentor2009 WHERE orden = <$_SESSION['orden2017']>
```

---

