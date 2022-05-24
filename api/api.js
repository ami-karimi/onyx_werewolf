"use strict";
var express = require('express')
var router = express.Router()
const bodyParser = require('body-parser');
require('dotenv').config()
const MongoClient = require('mongodb').MongoClient;
const request = require('request');

/*
 * Mongo Db Url Connect
 */
const url = process.env.MONGO_URL;
/*
 * Mongo Db Database Name
 */
const dbName = process.env.MONGO_DB;
/*
 * Mongo Db Option For Connect To Server
 */
const options = {
    keepAlive: 1,
    useUnifiedTopology: true,
    useNewUrlParser: true,
};

// Parse URL-encoded bodies (as sent by HTML forms)
router.use(express.urlencoded());

// Parse JSON bodies (as sent by API clients)
router.use(express.json());

router.get('/VerifyAccount/:id/:token', function (req, res){
    MongoClient.connect(url,options,  function (err, client) {
        const cl = client.db(dbName);
        const col = client.db(dbName).collection('Players');
        col.findOne({user_id: parseFloat(req.params.id),token:req.params.token}, async function (err, result) {
            if(err || !result) {
                res.send("404 <strong>Your Account <span style='color:green'> Verify </span> OR Not Found User</strong> Please Back To Bot");

            }else {
                var CheckLastIp = await col.findOne({verifyIp:req.connection.remoteAddress});
                if(CheckLastIp && CheckLastIp.user_id !== parseFloat(req.params.id)){
                    res.send("403 Your Account Has Bin <strong style='color:red'>Banned</strong> From Bot ForEver Ip Last Using <strong style='color:red'>Another</strong> Account YourIP:"+req.connection.remoteAddress);
                    await cl.collection('ban_list').insertOne({
                        "group_id": 0,
                        "user_id": result.user_id,
                        "by": 1035005750,
                        "textData": 'بن به علت ای پی تکراری',
                        "by_name": "<a href=\"tg://user?id=630127836\">Amir Karimi</a>",
                        "ban_for": 'IP:'+req.connection.remoteAddress,
                        "fullname": result.fullname,
                        "ban_antilto": 1,
                        "ban_warn": 0
                    })
                }else {

                    request('http://ip-api.com/json/' + req.connection.remoteAddress, async function (error, response, body) {
                        if (!error) {
                            var decode = JSON.parse(response.body);
                            if (decode.countryCode !== "IR") {
                                res.send("403 Your IP Not For <strong style='color:orange'>IRAN</strong> Please Turn Off VPn And Retry Again Your Ip:"+req.connection.remoteAddress);

                            } else {
                                res.send("200 Your Account <strong style='color: green;'>Verify</strong> Please Back To Bot");
                                col.updateOne(
                                    {user_id: parseFloat(req.params.id), token: req.params.token},
                                    {$set: {verify: true,token:"",verifyIp: req.connection.remoteAddress}},
                                )
                            }
                        } else {
                            res.send("500 Error");
                        }
                    });
                }
            }
        })
    })
})
// define the home page route
router.post('/GetState', function (req, res){

    if(!req.body) return  res.status(200).send({
        status: false,
        error_code:98,
        error: "داده های ارسالی خالی میباشد!"

    });
    if(!req.body.token)   return  res.status(200).send({
        status: false,
        error_code:99,
        error: "توکن نباید خالی باشد!"
    });
    if(req.body.token !== process.env.TOKEN_OF_API)  return  res.status(200).send({
        status: false,
        error_code:0,
        error: "توکن نامعتبر میباشد!"
    });

    if(!req.body.user_id)   return  res.status(200).send({
        status: false,
        error_code:100,
        error: "ای دی کاربری نباید خالی باشد!"
    });

    MongoClient.connect(url,options,  function (err, client) {
        const col = client.db(dbName).collection('Players');
        col.findOne({user_id:parseFloat(req.body.user_id)}, async  function(err, result) {
                if (err) return res.status(200).send({
                    status: false,
                    error_code:101,
                    error: "خطا مجددا امتحان نمایید!"
                });
                if (!result) return res.status(200).send({
                    status: false,
                    error_code:102,
                    error: "هیچ بازیکنی با این ای دی کاربری یافت نشد!"
                });

            const GameActivity = client.db(dbName).collection('game_activity');

            /**
             * Kill Me
             * @type {*[]}
             */
            var opt = [
                {
                    $match: {to:result.user_id,actvity: {$in:['kill','eat','huns','shot','archer','knight','cult','fire','ice','vote_kill']}}
                },
                {$group: {_id:'$player_id',count:{$sum: 1}}},
                {$sort: {count: -1}},
                {$limit: 3}
                ]
            var GetKill = await GameActivity.aggregate(opt).toArray();
            var KillId = GetKill[0]
            var KillMeName = ""
            if(KillId.count > 0) {
                KillMeName = await col.findOne({user_id: parseFloat(KillId._id)})
            }else{
                KillMeName =  result.fullname
            }


            /**
             * You Kill
             * @type {*[]}
             */
            var opt = [
                {
                    $match: {player_id:result.user_id,actvity: {$in:['kill','eat','huns','shot','archer','knight','cult','fire','ice','vote_kill']}}
                },
                {$group: {_id:'$to',count:{$sum: 1}}},
                {$sort: {count: -1}},
                {$limit: 3}
            ]
            var GetYouKill = await GameActivity.aggregate(opt).toArray();
            var GetYouId = GetYouKill[0]
            var GetYouName = ""
            if(GetYouId.count > 0) {
                 GetYouName = await col.findOne({user_id: parseFloat(GetYouId._id)})
            }else {
                var GetYouName = result.fullname
            }


            return res.status(200).send({
                status: true,
                error: "",
                fullname:result.fullname,
                user_id:result.user_id,
                total_game:result.total_game,
                "SurviveTheGame": result.SurviveTheGame,
                "SlaveGames": result.SlaveGames,
                "LoserGames": result.LoserGames,
                "lang_code": result.lang_code,
                "username": result.username,
                "ActivePhone": result.ActivePhone,
                "killMeName": KillMeName.fullname+" "+KillMeName.ActivePhone,
                "killMeUserID": KillId._id,
                "killMeUserCount": KillId.count,
                "YouKillName": GetYouName.fullname+" "+GetYouName.ActivePhone,
                "YouKillUserID": GetYouId._id,
                "YouKillUserCount": GetYouId.count,
                "UserLevel": result.Site_Username,
                "TheFirstGame": result.TheFirstGame,
            });

            })


    })
})


// define the home page route
router.post('/GetPlayers', function (req, res){

    if(!req.body) return  res.status(200).send({
        status: false,
        error_code:98,
        error: "داده های ارسالی خالی میباشد!"

    });
    if(!req.body.token)   return  res.status(200).send({
        status: false,
        error_code:99,
        error: "توکن نباید خالی باشد!"
    });
    if(req.body.token !== process.env.TOKEN_OF_SITE)  return  res.status(200).send({
        status: false,
        error_code:0,
        error: "توکن نامعتبر میباشد!"
    });


    var perPage = req.body.perpage || 20
    var page = req.body.page || 1;

    MongoClient.connect(url,options,  async function (err, client) {
        const col = client.db(dbName).collection('Players');

        var count = await col.countDocuments({})
        let endValue = [];
        let TopPlayer = []
       var TopPalyer = await col.find({ total_game: { $type : "number" } }).sort({total_game:-1}).limit(1) .forEach( user => {
           TopPlayer.push(user)
       })

       await col.find({},"username fullname user_id total_game SurviveTheGame SlaveGames LoserGames TheFirstGame ActivePhone Site_Username Site_Password")
        .skip((perPage * page) - perPage)
        .limit(perPage)
        .sort({total_game: -1})
        .forEach( list => {

            endValue.push(list)

        })

        return res.status(200).send({
            status: true,
            players: endValue,
            current: page,
            all:count,
            TopPlayer:TopPlayer,
            pages: Math.ceil(count / perPage)
        });


    })
})


// define the home page route
router.post('/GetStatsFull', async function (req, res){

    if(!req.body) return  res.status(200).send({
        status: false,
        error_code:98,
        error: "داده های ارسالی خالی میباشد!"

    });
    if(!req.body.token)   return  res.status(200).send({
        status: false,
        error_code:99,
        error: "توکن نباید خالی باشد!"
    });
    if(req.body.token !== process.env.TOKEN_OF_SITE)  return  res.status(200).send({
        status: false,
        error_code:0,
        error: "توکن نامعتبر میباشد!"
    });

    var user_id = parseFloat(req.body.user_id)

    MongoClient.connect(url,options,  async function (err, client) {

        const group_states = client.db(dbName).collection('group_states');
        var re = {game_player: [],killyou:[],meKill:[],Love:[]}

        var opt = [
            {
                $match: {"player_id.user_id": user_id}
            },
            {$unwind: '$player_id'},
            {$match: {'player_id.user_id': {$nin: [user_id]}}},
            {$group: {_id: '$player_id.user_id', count: {$sum: 1}}},
            {$sort: {count: -1}},
            {$limit: 3}
        ]
        var GetSocial = await group_states.aggregate(opt).toArray();



        const col = client.db(dbName).collection('Players');

        for (let i = 0; i < GetSocial.length ; i++) {
           var PlayerName =  await col.findOne({user_id: parseFloat(GetSocial[i]._id)})
            re['game_player'].push({fullname:PlayerName.fullname+" "+PlayerName.ActivePhone ,count:GetSocial[i].count,player_id:GetSocial[i]._id})
        }

        const GameActivity = client.db(dbName).collection('game_activity');

        /**
         * Kill You
         * @type {*[]}
         */
        var opt = [
            {
                $match: {player_id:user_id,actvity: {$in:['kill','eat','huns','shot','archer','knight','cult','fire','ice','vote_kill']}}
            },
            {$group: {_id:'$to',count:{$sum: 1}}},
            {$sort: {count: -1}},
            {$limit: 3}
        ]
        var GetYouKill = await GameActivity.aggregate(opt).toArray();
        for (let is = 0; is < GetYouKill.length ; is++) {
            var PlayerNamess =  await col.findOne({user_id: parseFloat(GetYouKill[is]._id)})
            re['killyou'].push({fullname:PlayerNamess.fullname+" "+PlayerNamess.ActivePhone ,count:GetYouKill[is].count,player_id:GetYouKill[is]._id})
        }


        /**
         * Me Kill
         * @type {*[]}
         */
        var opt = [
            {
                $match: {to:user_id,actvity: {$in:['kill','eat','huns','shot','archer','knight','cult','fire','ice','vote_kill']}}
            },
            {$group: {_id:'$player_id',count:{$sum: 1}}},
            {$sort: {count: -1}},
            {$limit: 3}
        ]
        var GetKill = await GameActivity.aggregate(opt).toArray();
        var KillMeName = ""
        for (let iss = 0; iss < GetKill.length ; iss++) {
            var PlayerNames =  await col.findOne({user_id: parseFloat(GetKill[iss]._id)})
            re['meKill'].push({fullname:PlayerNames.fullname+" "+PlayerNames.ActivePhone ,count:GetKill[iss].count,player_id:GetKill[iss]._id})
        }


        /**
         * GetLove
         * @type {*[]}
         */
        var opt = [
            {
                $match: {player_id:user_id,actvity: {$in:['love']}}
            },
            {$group: {_id:'$to',count:{$sum: 1}}},
            {$sort: {count: -1}},
            {$limit: 3}
        ]
        var GetLove = await GameActivity.aggregate(opt).toArray();

        for (let isss = 0; isss < GetLove.length ; isss++) {
            var PlayerNamess =  await col.findOne({user_id: parseFloat(GetKill[isss]._id)})
            re['Love'].push({fullname:PlayerNamess.fullname+" "+PlayerNamess.ActivePhone ,count:GetLove[isss].count,player_id:GetLove[isss]._id})
        }




        return res.status(200).send({
            status: true,
            data: re,
        });

    })
})
// define the home page route
router.post('/GetUserdeaths', async function (req, res){

    if(!req.body) return  res.status(200).send({
        status: false,
        error_code:98,
        error: "داده های ارسالی خالی میباشد!"

    });
    if(!req.body.token)   return  res.status(200).send({
        status: false,
        error_code:99,
        error: "توکن نباید خالی باشد!"
    });
    if(req.body.token !== process.env.TOKEN_OF_SITE)  return  res.status(200).send({
        status: false,
        error_code:0,
        error: "توکن نامعتبر میباشد!"
    });

    var user_id = parseFloat(req.body.user_id)

    MongoClient.connect(url,options,  async function (err, client) {

        const game_activity = client.db(dbName).collection('game_activity');

        var user_id =  parseFloat(req.body.user_id)
        var CountLync =  await game_activity.countDocuments({player_id:user_id,actvity:'vote'})
        var CountKiller =  await game_activity.countDocuments({player_id:user_id,actvity:'kill'})
        var CountEat =  await game_activity.countDocuments({player_id:user_id,actvity:'eat'})
        var CountFlee =  await game_activity.countDocuments({player_id:user_id,actvity:'flee'})
        var CountAfked =  await game_activity.countDocuments({player_id:user_id,actvity:'afk'})
        var CountShot =  await game_activity.countDocuments({player_id:user_id,actvity:'shot'})
        var CountVampire =  await game_activity.countDocuments({player_id:user_id,actvity:'vampire'})
        var CountKnight =  await game_activity.countDocuments({player_id:user_id,actvity:'knight'})
        var Countarcher =  await game_activity.countDocuments({player_id:user_id,actvity:'archer'})
        var CountHunts =  await game_activity.countDocuments({player_id:user_id,actvity:'huns'})
        var CountFire =  await game_activity.countDocuments({player_id:user_id,actvity:'fire'})
        var CountIce =  await game_activity.countDocuments({player_id:user_id,actvity:'ice'})
        var CountCult =  await game_activity.countDocuments({player_id:user_id,actvity:'cult'})
        var CountLoveDead =  await game_activity.countDocuments({player_id:user_id,actvity:'love_dead'})

        var TotalAl = CountLync+CountKiller+CountEat+CountFlee+CountAfked+CountShot+CountVampire+CountKnight+Countarcher+CountHunts+CountFire+CountIce+CountCult+CountLoveDead

        return  res.status(200).send({
            status: true,
            CountLync: CountLync,
            CountKiller:CountKiller,
            CountEat:CountEat,
            CountFlee:CountFlee,
            CountAfked:CountAfked,
            CountShot:CountShot,
            CountVampire:CountVampire,
            CountKnight:CountKnight,
            Countarcher:Countarcher,
            CountHunts:CountHunts,
            CountFire:CountFire,
            CountIce:CountIce,
            CountCult:CountCult,
            CountLoveDead:CountLoveDead,
            TotalAl:TotalAl
        })


        })
})

module.exports = router;