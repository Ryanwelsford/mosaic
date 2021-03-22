<!DOCTYPE html>
<head>

    <link rel="stylesheet" href="/styles/styles.css">
    <link rel="icon" href="/images/phr-logo.svg" sizes="16x16" type="image/png">
    <title>
        @hasSection("title")
            @yield("title")
        @else
            {{ "Pizza Hut Mosaic" }}
        @endif
    </title>

</head>
