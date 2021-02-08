var cron = require('node-schedule');
/* run the job at 18:55:30 on Dec. 14 2018*/
var date = new Date(2019, 04, 07, 22, 46, 00);
cron.scheduleJob(date, function(){
    console.log(new Date(), "Somthing important is going to happen today!");    
});