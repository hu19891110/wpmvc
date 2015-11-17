// Load dependencies
var express = require('express');
var webpack = require('webpack');
var webpackConfig = require('./webpack.config');
var webpackDevMiddleware = require('webpack-dev-middleware');
var webpackHotMiddleware = require('webpack-hot-middleware');
var webpackCompiler = webpack(webpackConfig);

// Create the express app
var app = express();

// Attach webpack-dev-middleware and webpack-hot-middleware
app.use(webpackDevMiddleware(webpackCompiler, {
    noInfo: true,
    watchOptions: {
        aggregateTimeout: 1000,
        poll: 1000
    }
}));
app.use(webpackHotMiddleware(webpackCompiler));

// Create static directories
app.use('/dist', express.static(__dirname + '/dist'));

// Set the address/port
var address = process.env.DOCKER_HOST || 'localhost';
var port = process.env.PORT || 4000

// Listen to the port
var server = app.listen(port, function(err, result) {
    if (err) {
        console.log(err);
    }
  console.log('Running on http://%s:%s', address, port);
});
