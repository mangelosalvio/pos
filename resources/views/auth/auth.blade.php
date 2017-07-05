<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ Config::get('constants.COMPANY_NAME') }}</title>
    {!! HTML::script('bower_components/webcomponentsjs/webcomponents.min.js') !!}
    <link rel="import" href="/bower_components/iron-flex-layout/classes/iron-flex-layout.html">
    <link rel="import" href="/bower_components/iron-icons/iron-icons.html"/>
    <link rel="import" href="/bower_components/iron-icons/communication-icons.html">
    <link rel="import" href="/bower_components/iron-icons/social-icons.html">

    <link rel="import" href="/bower_components/paper-card/paper-card.html"/>
    <link rel="import" href="/bower_components/paper-input/paper-input.html"/>
    <link rel="import" href="/bower_components/paper-button/paper-button.html"/>
    <link rel="import" href="/bower_components/paper-menu/paper-menu.html"/>
    <link rel="import" href="/bower_components/paper-item/paper-item.html"/>
    <link rel="import" href="/bower_components/paper-drawer-panel/paper-drawer-panel.html"/>
    <link rel="import" href="/bower_components/paper-header-panel/paper-header-panel.html"/>
    <link rel="import" href="/bower_components/paper-icon-button/paper-icon-button.html"/>
    <link rel="import" href="/bower_components/paper-scroll-header-panel/paper-scroll-header-panel.html"/>
    <link rel="import" href="/bower_components/paper-styles/color.html"/>
</head>
<style is="custom-style">
    body {
        font-family: 'Roboto', 'Noto', sans-serif;
        font-size: .9vw;
        margin: 0;
    }

    paper-card{
        --paper-card-header: {
             @apply(--layout-vertical);
             @apply(--layout-center);
         };


    }

    paper-button {
        margin-top: 30px;
        padding:0;
        width:100%;
        background-color: var(--google-green-500);

    }
    paper-button::shadow .button-content {
        padding:0;

    }
    paper-button button {
        background-color: transparent;
        border-color: transparent;
        color:#fff;
    }
    paper-button button::-moz-focus-inner {
        border: 0;
    }
</style>
    @yield('content')
</html>