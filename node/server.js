const assert = require('assert');
var schedule = require('node-schedule');
var cron = require('node-cron');
var request = require('request');
const MongoClient = require('mongodb').MongoClient;
const url = 'mongodb://127.0.0.1:27017';
// Database Name
const dbName = 'wop';
var cron = require('node-cron');
const fs = require('fs');


function sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}

cron.schedule('30 22 * * 5 ', function() {
    request.post(
        'http://YOUR_SITE_ADDRESS/CronBot.php',
        {json: {
                action: 'group_list_update',
                key: 's109v@#A45adsd'
            }
        }
        ,
        function (error, response, body) {
            if (!error && response.statusCode == 200) {
                if (body) {
                    console.log(body)
                }
            }

        }
    );
})

cron.schedule('00 00  * * * ', function() {
    request.post(
        'http://YOUR_SITE_ADDRESS/CronBot.php',
        {json: {
                action: 'group_list_history',
                key: 's109v@#A45adsd'
            }
        }
        ,
        function (error, response, body) {
            if (!error && response.statusCode == 200) {
                if (body) {
                    console.log(body)
                }
            }

        }
    );
})
cron.schedule('00 00  * * * ', function() {
    request.post(
        'http://YOUR_SITE_ADDRESS/CronBot.php',
        {json: {
                action: 'reset_send_list',
                key: 's109v@#A45adsd'
            }
        }
        ,
        function (error, response, body) {
            if (!error && response.statusCode == 200) {
                if (body) {
                    console.log(body)
                }
            }

        }
    );
})



var datetime = new Date();
    MongoClient.connect(url,{ useUnifiedTopology: true,useNewUrlParser: true  }, function (err, client) {
    assert.equal(null, err);
    const col = client.db(dbName).collection('games');
    var j = schedule.scheduleJob('*/5 * * * * *', function() {
        col.find({}).toArray(function (err, items) {

            items.forEach(function (rows) {

                request.post(
                    'https://YOUR_SITE_ADDRESS/Cron.php',
                    {json: rows},
                    function (error, response, body) {

                        if (!error && response.statusCode == 200) {
                            if(body) {
                                console.log(body)
                            }
                        }

                    }
                );

            });


        });
    });

});


MongoClient.connect(url,{ useUnifiedTopology: true,useNewUrlParser: true  }, function (err, client) {
    assert.equal(null, err);
    const col = client.db(dbName).collection('bet_game');
    var j = schedule.scheduleJob('*/3 * * * * *', function() {
        col.find({}).toArray(function (err, items) {

            items.forEach(function (rows) {

                request.post(
                    'http://YOUR_SITE_ADDRESS/CronBot.php',
                    {json: {
                            action: 'bet_update',
                            key: 's109v@#A45adsd'
                        }},
                    function (error, response, body) {

                        if (!error && response.statusCode == 200) {
                            if(body) {
                                console.log(body)
                            }
                        }


                    }
                );

            });


        });
    });

});
