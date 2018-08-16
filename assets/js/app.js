// function to search something in the net
function google(){
    window.open('http://www.google.it', "_blank");
}

// function to say 'hello'
function hello(str){

    if (str == "on") {
    
        $('#b5').attr('id','b5hello');
        $('#b51').attr('id','b51hello');
        $('#b51').attr('id','b51hello');
        $('#b51').attr('id','b51hello');
        $('#b51').attr('id','b51hello');
        $('#b51').attr('id','b51hello');
    
    }else{
    
        $('#b5hello').attr('id','b5');
        $('#b51hello').attr('id','b51');
        $('#b51hello').attr('id','b51');
        $('#b51hello').attr('id','b51');
        $('#b51hello').attr('id','b51');
        $('#b51hello').attr('id','b51');
    
    };
    
}

// function to move the mounth while speaking
function speak(str){

    if (str == "on") {
    
        $('#m1').attr('id','m1speak');
        $('#m2').attr('id','m2speak');
    
    }else{
    
        $('#m1speak').attr('id','m1');
        $('#m2speak').attr('id','m2');
    
    };
    
}

// function to gaze
function gaze(str){

    if (str == "on") {
    
        $('#e1').attr('id','e1fixed');
        $('#e2').attr('id','e2fixed');
        $('#eye').attr('id','eyefixed');
        $('#eye').attr('id','eyefixed');
    
    
    }else{
    
        $('#e1fixed').attr('id','e1');
        $('#e2fixed').attr('id','e2');
        $('#eyefixed').attr('id','eye');
        $('#eyefixed').attr('id','eye');
    
    };
    
}

// function to dance
function dance(str){

    if (str == "on") {

        $('#leg1').attr('id','leg1dance');
        $('#leg1').attr('id','leg1dance');
        $('#leg1').attr('id','leg1dance');
        $('#leg1').attr('id','leg1dance');
        $('#leg1').attr('id','leg1dance');
        $('#leg2').attr('id','leg2dance');
        $('#leg2').attr('id','leg2dance');
        $('#leg2').attr('id','leg2dance');
        $('#leg2').attr('id','leg2dance');
        $('#leg2').attr('id','leg2dance');
        $('#body').attr('id','bodydance');
        $('.left').removeClass('left').addClass('leftdance');
        $('.right').removeClass('right').addClass('rightdance');
        $('#b5').attr('id','b5dance');
        $('#b51').attr('id','b51dance');
        $('#b7').attr('id','b7dance');
        $('#b71').attr('id','b71dance');   

    }else{

        $('#leg1dance').attr('id','leg1');
        $('#leg1dance').attr('id','leg1');
        $('#leg1dance').attr('id','leg1');
        $('#leg1dance').attr('id','leg1');
        $('#leg1dance').attr('id','leg1');
        $('#leg2dance').attr('id','leg2');
        $('#leg2dance').attr('id','leg2');
        $('#leg2dance').attr('id','leg2');
        $('#leg2dance').attr('id','leg2');
        $('#leg2dance').attr('id','leg2');
        $('#bodydance').attr('id','body');
        $('.leftdance').removeClass('leftdance').addClass('left');
        $('.rightdance').removeClass('rightdance').addClass('right');
        $('#b5dance').attr('id','b5');
        $('#b51dance').attr('id','b51');
        $('#b7dance').attr('id','b7');
        $('#b71dance').attr('id','b71');

    };
    
}

// function to send vote to the database
function add_vote(v){
    var reply = $('.reply-content').html();
    var question = $('#question').attr('placeholder');

    $.ajax({
        type: "POST",
        url: 'vote.php',
        data: ({ vote : v, question: question, reply: reply }),
        dataType: "json",
        success: function(data) {
          
          if (data.error == false) {

            $('.vote-result').html("<h5 class='"+data.color+"'>"+data.value+"</h5>");

          };
          
        },
        error: function() {
          
            console.log('error - ajax vote error');
            // try again
            setTimeout(function(){ add_vote(v); }, 2000);

        }
    });
}

// function to add stars after the reply
function enable_vote(){
    $('#reply').append("<div class='starrr'></div>");

    setTimeout(function(){
        $(".starrr").starrr();
        $('.starrr').append('<div class="vote-result"></div>');

        $('.starrr').on('starrr:change', function(e, value){
            add_vote(value);
        });

    }, 50);
}

// function to reply the current time
function time(){
    var today = new Date();

    reply(today.getHours() + ":" + today.getMinutes() + ":" + today.getSeconds());
}

// function to reply the current date
function day(){
    var days = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
    var months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];

    var today = new Date();

    reply(days[today.getDay()] + " " +today.getDate() + " " + months[today.getMonth()] + " " + today.getFullYear());
}

// function to do something 
// an action will be done
function todo(action, other){

    action = parseInt(action);

    switch(action){

    case 1: hello('on'); setTimeout(function(){ hello('off');  }, 6000); break;
    case 2: dance('on'); setTimeout(function(){ dance('off');  }, 12000); break;
    case 3: time(); break;
    case 4: gaze('on'); setTimeout(function(){ gaze('off');  }, 6000); break;
    case 5: day(); break;
    case 6: google(); break;

    default:
        
        // something else
        $.ajax({
            type: "POST",
            url: 'action.php',
            data: ({ action : action }),
            dataType: "json",
            success: function(data) {
            
                // do nothing
                // something done in php file
            
            },
            error: function() {
                
            console.log('error - ajax action error');
            // try again
            setTimeout(function(){ todo(action); }, 2000);

            }
        });
        break;
    }

}

// function to print and speak reply
// print: insert reply in the correct space
// speak: move mounth and speak with the speakers
function reply(text, lang = 'en'){
    speak('on');

    $('#reply').html('<h1 class="reply-content">'+text+'</h1>');

    var u = new SpeechSynthesisUtterance();
    u.text = text;
    u.lang = lang;
    u.rate = 1.2;
    u.onend = function(event) {
        speak('off');
    }
}

// main function
// function to get question and find a reply
function ask(text){
    
    $('#question').attr('placeholder', text);
    $('#question').val('');


    text = text.trim().toLowerCase();
    $('#reply').html('<div id="movingBallG" style="display:block; text-align: center; margin: auto;"><div class="movingBallLineG"></div><div id="movingBallG_1" class="movingBallG"></div></div>'); // Loading
    
    $.ajax({
        type: "POST",
        url: 'brain.php',
        data: ({ question : text }),
        dataType: "json",
        success: function(data) {
         
            if (data.reply != "" || (data.action != null && data.action != false)) {
                reply(data.reply);
                if (data.action != null && data.action != false) {
                    todo(data.action);
                }
                else if(data.enable_vote) { enable_vote(); };
            };
          
        },
        error: function() {
            
            console.log('error - ajax brain error');
            // try again
            setTimeout(function(){ ask(text); }, 2000);

        }
    });
                 
}

// function to learn new question/reply
// using Twitter API
function learn(){

    setTimeout(function(){
    
        $('#learn').addClass('on').removeClass('off');
        $('#learn').html("Learning...");
        
        $.ajax({
            type: 'POST',
            url: 'learn.php',
            cache: false,
            dataType: 'json',
            data:
            {
        
            },
            success:function(data) {       
                $('#learn').html("Learn now");
                $('#learn').addClass('off').removeClass('on');
               
                if(data.success){
                    $('#message').html(data.info.message);
                    $('#error').html('');
                }
                else{
                    $('#message').html('');
                    $('#error').html(data.info.error);
                }

                if(data.recursive) learn();
            },
            error: function(request, state, errors){

                $('#error').html('server error');

                console.log(request);
                console.log(state);
                console.log(errors);
        
                learn();
        
            }
        });
        
    }, 500);
            
}