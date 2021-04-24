<!DOCTYPE html>
<head>

    <link rel="stylesheet" href="/styles/styles.css">
    <link rel="icon" href="/images/phr-logo.svg" sizes="16x16" type="image/png">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.3/css/all.css" integrity="sha384-SZXxX4whJ79/gErwcOYf+zWLeJdY/qpuqC4cAa9rOGUstPomtqpuNWT9wdPEn2fk" crossorigin="anonymous">
    <script src="/scripts/login.js"></script>
    <title>
        @hasSection("title")
            @yield("title")
        @else
            {{ "Pizza Hut Mosaic" }}
        @endif
    </title>

</head>
