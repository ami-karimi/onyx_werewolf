var mongoose = require('mongoose')

var Schema = mongoose.Schema;

var GroupsSchema = new Schema({
    chat_id: String,
    chat_name: String,
    chat_type: String,
    user_added_id: String,
    user_added_username: String,
    user_added_fullname: String,
    date: String,
    jdate:  { type: Date, default: Date.now },
    status: Number,
}, { timestamps: { createdAt: 'created_at' }})

module.exports = mongoose.model('groups', GroupsSchema)