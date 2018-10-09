var urlvar = getUrlVars();
var $visitor = '';
var elements = ["h1, h2, h3, h4, h5, h6, p, li, strong"];
var purl_options_url = '';

$(document).ready(function() {

    var test = 'N';

    if(urlvar['purl'] && urlvar['campaign']) {
        var purl = urlvar['purl'];
        var campaign = urlvar['campaign'];
        if(urlvar['test'] == 'Y') test = 'Y';
    }
    else if(cookie = getCookie('student')) {
        cookie = JSON.parse(cookie);
        if(cookie.campaign_id && cookie.purl) {
            var purl = cookie.purl;
            var campaign = cookie.campaign_id;
            test = getCookie('test');
        } else {
            return;
        }
    }

    if(test == 'Y') {
        $('body').prepend('<div id="test" style="width:100%;background-color:gray;color:white;padding:3px;text-align:center;">Testing Mode <a href="" id="test_off">Turn Off</a></div>')
    }
    //console.log('Test: ' + test);

    var i=0;
    while (elements.length>i) {
        var element = elements[i];
        $(element).hide();
        i++;
    }
    $('input').each(function() {
        $(this).hide();
    });

    //$('body').hide();

    var url = window.location.href;
    var path = window.location.pathname;

    //For local testing
    if(document.domain == 'recruitu') {
        var purl_tracking_url = '/api/lp/visitor?purl='+purl+'&campaign='+campaign+'&url='+url+'&path='+path+'&test='+test;
        var purl_submit_url = '/api/lp/submit';
        var purl_track_link_url = '/api/lp/link';
        purl_options_url = '/api/lp/options';
    }
    //For production
    else {
        var purl_tracking_url = 'http://betterstudentrecruiting.com/api/lp/visitor?purl='+purl+'&campaign='+campaign+'&url='+url+'&path='+path+'&test='+test;
        var purl_submit_url = 'http://betterstudentrecruiting.com/api/lp/submit';
        var purl_track_link_url = 'http://betterstudentrecruiting.com/api/lp/link';
        purl_options_url = 'http://betterstudentrecruiting.com/api/lp/options';
    }

    console.log(purl_tracking_url);

    $.ajax({
        url: purl_tracking_url,
        dataType: "json",
        success: function(visitor){
            $visitor = visitor;
            setCookie('student',JSON.stringify(visitor),30);
            setCookie('test',test);
            if(!visitor.firstName) nopurl(); //Call this optional function if no purl is found. Used to redirect to other page if no PURL.
            if(visitor.converted && visitor.redirect_page) window.location = visitor.redirect_page;
            personalizePage();
            if(test == 'Y') {
                getOptions();
            }
        },
        error: function(error){
            console.log("Error:");
            console.log(error);
        }
    });


    $('form').submit(function(e) {
        // this code prevents form from actually being submitted
        e.preventDefault();
        e.returnValue = false;

        //Continue without tracking if testing mode is on
        if(test == 'Y') this.submit();

        var $form = $(this);
        var data = $form.find(":input:not(:hidden)").serialize();
        data = data+'&purl_campaign='+campaign+'&student_id='+$visitor.id;

        // this is the important part. you want to submit
        // the form but only after the ajax call is completed
        $.ajax({
            type: 'post',
            url: purl_submit_url,
            data: data,
            context: $form, // context will be "this" in your handlers
            success: function(response) { // your success handler
                console.log('Success: '+response);
            },
            error: function(response) { // your error handler
                console.log('Error: '+response);
            },
            complete: function() {
                // make sure that you are no longer handling the submit event; clear handler
                this.off('submit');
                // actually submit the form
                this.submit();
            }
        });
    });

    $('a').click(function(e) {
        // this code prevents form from actually being submitted
        e.preventDefault();
        e.returnValue = false;

        var $link = $(this).attr('href');

        if($(this).attr('id') == 'test_off') {
            $('#test').hide();
            setCookie('test','N');
            window.location = window.location.href.replace('&test=Y','');
            return false;
        }

        //Continue without tracking if testing mode is on
        if(test == 'Y') window.location = $link;

        // this is the important part. you want to click
        // the link but only after the ajax call is completed
        $.ajax({
            type: 'post',
            url: purl_track_link_url,
            data: { link: $link, 'student_id': $visitor.id },
            success: function(response) { // your success handler
                console.log('Success: '+response);
            },
            error: function(response) { // your error handler
                console.log('Error: '+response);
            },
            complete: function() {
                window.location = $link;
            }
        });
    });

});

function getOptions() {
    $("select").each(function(){
        var name = $(this).attr('name');
        if(!name) return true;
        console.log('* '+name);

        var options = [];
        $(this).children('option').each( function() {
            if(jQuery.inArray($(this).val(), options) == -1) {
                options.push($(this).val());
            }
        });

        console.log(options);
        sendOptions(name, options);
    });

    var option_inputs = [];
    $(":input").each(function(){
        var name = $(this).attr('name');

        if(!name) return true;

        var type = $(this).attr('type');
        if(type == 'checkbox' || type == 'radio') {
            if(jQuery.inArray(name, option_inputs) == -1) {
                option_inputs.push(name);
            }

        }
    });

    $.each(option_inputs, function( index, name ) {
        console.log('* '+name);
        var options = [];
        $('input[name="'+name+'"]').each( function() {
            if(jQuery.inArray($(this).val(), options) == -1) {
                options.push($(this).val());
            }
        });

        console.log(options);
        sendOptions(name, options);
    });
}

function sendOptions(input_name, options) {
    $.ajax({
        type: 'post',
        url: purl_options_url,
        data: { input_name: input_name, 'options': options, 'campaign_id': $visitor.campaign_id },
        success: function(response) { // your success handler
            console.log('Success: '+response);
        },
        error: function(response) { // your error handler
            console.log('Error: '+response);
        }
    });
}

function personalizePage() {
    console.log($visitor);

    var i=0;
    while (elements.length>i) {

        var element = elements[i];

        //Content
        new_content = purlConvert($(element).html(),$visitor);
        $(element).html(new_content);

        //Image src
        if($(element).attr('src')) {
            new_src = purlConvert($(element).attr('src'),$visitor);
            $(element).attr('src',new_src);
        }

        //Style
        if($(element).attr('style')) {
            new_style = purlConvert($(element).attr('style'),$visitor);
            $(element).attr('style',new_style);
        }

        //Loop through If Statements
        var reg = /\*\|(IF:[\s\S]*?)\*\|END:IF\|\*/g;

        new_content = $(element).html().replace(reg, function($statement){


            return testStatement($statement);

        });
        $(element).html(new_content);

        $(element).show();

        i++;
    }

    $('input').each(function() {
        this.value = purlConvert(this.value,$visitor);
        if(this.value.indexOf('*|') > -1) this.value = '';
        $(this).show();
    });


    //$('body').show();
}

function testStatement(statement) {

    var response = '';

    var conditions = statement.split('*|');
    $.each(conditions,function(i){
        var condition = conditions[i];

        var match_regex = /IF:(.*)=(.*)\|\*([\s\S]*)/;
        if(match_regex.test(condition)) {
            results = match_regex.exec(condition);
            var tag = results[1].trim().toLowerCase();
            var value = results[2].trim().toLowerCase();
            var content = results[3].trim();

            //console.log($visitor[tag.toUpperCase()].toLowerCase()+' = '+value+' ? '+content);
            if($visitor[tag.toUpperCase()].toLowerCase() == value) {
                response = content;
                return;
            }
        }

        if(!response) {
            var match_regex = /ELSE:\|\*([\s\S]*)/;
            if(match_regex.test(condition)) {
                //console.log('else');
                results = match_regex.exec(condition);
                var content = results[1].trim();
                response = content;
                return;
            }
        }

    });




    return response;
}

function purlConvert(content, visitor) {

    $.each(visitor, function(key, value) {
        if(key == 'firstName') key = 'FNAME';
        if(key == 'lastName') key = 'LNAME';

        //console.log(key+' '+value);
        content = content.replace('*|'+key.toUpperCase()+'|*',value);

    });



    return content;
}
function getUrlVars()
{
    var vars = [], hash;
    var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
    for(var i = 0; i < hashes.length; i++)
    {
        hash = hashes[i].split('=');
        vars.push(hash[0]);
        vars[hash[0]] = hash[1];
    }
    return vars;
}

function nopurl() {
    //placeholder function in case the user does not define one
}

function setCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays*24*60*60*1000));
    var expires = "expires="+ d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}

function getCookie(cname) {
    var name = cname + "=";
    var decodedCookie = decodeURIComponent(document.cookie);
    var ca = decodedCookie.split(';');
    for(var i = 0; i <ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}