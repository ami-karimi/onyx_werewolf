"use strict";

require('dotenv').config()
const express = require('express');
const bodyParser = require('body-parser');
const MongoClient = require('mongodb').MongoClient;
const assert = require('assert');
const cors = require('cors')
const mongoSanitize = require('express-mongo-sanitize');
const xss = require('xss-clean')

/**
 * App Variables
 */

// Mongo Db Setting
const url = process.env.MONGO_URL; //Mongo Url
const options = {
    keepAlive: 1,
    useUnifiedTopology: true,
    useNewUrlParser: true,
};


// My App
const app = express();

// Router App
const router = express.Router();

// Header Access Control
app.use(function(req, res, next) {
    res.header("Access-Control-Allow-Origin", "*");
    res.header("Access-Control-Allow-Headers", "Origin, X-Requested-With, Content-Type, Accept");
    next();
});

// Security App
app.use(xss());
app.set('view engine', 'ejs')
app.use(cors()) // cool now everything is handled!



router.use(bodyParser.json());
router.use(bodyParser.urlencoded());
router.use(bodyParser.urlencoded({ extended: true }));



// App Port Run
app.use(mongoSanitize({
    replaceWith: '_'
}))
let port = process.env.PORT ;
const dbName = process.env.MONGO_DB;
app.listen(port,"api.SITE_ADDRESS", function() {
    console.log('LOG: Express server listening on port ' + port)
});


// Use connect method to connect to the server
MongoClient.connect(url,options, function(err, client) {
    assert.equal(null, err);
    console.log("LOG: Connect Success Mongo DB Server");
    client.close();
});

// Api Location
const api = require('./api');
app.use('/api', api)
