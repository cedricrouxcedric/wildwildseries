/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you require will output into a single css file (app.css in this case)
require('../css/app.scss');
require('bootstrap');
// require 2 CSS files needed


// Need jQuery? Install it with "yarn add jquery", then uncomment to require it.
const $ = require('jquery');

$(document).ready(function() {
    $('[data-toggle="popover"]').popover();
});
// require the JavaScript

console.log('Hello Webpack Encore! Edit me in assets/js/app.js');
