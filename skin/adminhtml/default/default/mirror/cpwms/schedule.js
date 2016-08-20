var minute = '*';
var hour = '*';
var day = '*';
var month = '*';

function radioClicked(object){
    var selects = document.getElementsByClassName('schedule-select');
    for(var i = 0; i < selects.length; i++){
        switch(selects[i].name){
            case 'minute':
                var minuteSelect = selects[i];
                break;
            case 'hour':
                var hourSelect = selects[i];
                break;
            case 'day':
                var daySelect = selects[i];
                break;
            case 'month':
                var monthSelect = selects[i];
                break;
        }
    }
    switch(object.value){
        case 'Every Minute':
            minuteSelect.disable();
            minute = '*';
            break;
        case 'Every Hour':
            hourSelect.disable();
            hour = '*';
            break;
        case 'Every Day':
            daySelect.disable();
            day = '*';
            break;
        case 'Every Month':
            monthSelect.disable();
            month = '*';
            break;
        case 'Not Every Minute':
            minuteSelect.enable();
            minute = '';
            break;
        case 'Not Every Hour':
            hourSelect.enable();
            hour = '';
            break;
        case 'Not Every Day':
            daySelect.enable();
            day = '';
            break;
        case 'Not Every Month':
            monthSelect.enable();
            month = '';
            break;
    }
    createCronLine();
}

function setScheduleValue(value){
    var inputElements = document.getElementsByTagName('input');
    for(var i = 0; i < inputElements.length; i++){
        if(inputElements[i].type == 'radio'){
            switch(inputElements[i].value){
                case 'Every Minute':
                    var minuteRadioOff = inputElements[i];
                    break;
                case 'Not Every Minute':
                    var minuteRadioOn = inputElements[i];
                    break;
                case 'Every Hour':
                    var hourRadioOff = inputElements[i];
                    break;
                case 'Not Every Hour':
                    var hourRadioOn = inputElements[i];
                    break;
                case 'Every Day':
                    var dayRadioOff = inputElements[i];
                    break;
                case 'Not Every Day':
                    var dayRadioOn = inputElements[i];
                    break;
                case 'Every Month':
                    var monthRadioOff = inputElements[i];
                    break;
                case 'Not Every Month':
                    var monthRadioOn = inputElements[i];
                    break;
            }
        }
    }

    var input = value.split(' ');
    input[0] = input[0].substr(0,input[0].length - 5);
    input[1] = input[1].substr(0,input[1].length - 3);
    input[2] = input[2].substr(0,input[2].length - 4);
    input[3] = input[3].substr(0,input[3].length - 6);
    if(input[3] == '*'){
        minuteRadioOff.click();
        document.getElementById('schedule-select-minute-1').selected = true;
    } else{
        minuteRadioOn.click();
        document.getElementById('schedule-select-minute-'+input[3]).selected = true;
    }
    if(input[2] == '*'){
        hourRadioOff.click();
        document.getElementById('schedule-select-hour-1').selected = true;
    } else{
        hourRadioOn.click();
        document.getElementById('schedule-select-hour-'+input[2]).selected = true;
    }
    if(input[1] == '*'){
        dayRadioOff.click();
        document.getElementById('schedule-select-day-1').selected = true;
    } else{
        dayRadioOn.click();
        document.getElementById('schedule-select-day-'+input[1]).selected = true;
    }
    if(input[0] == '*'){
        monthRadioOff.click();
        document.getElementById('schedule-select-month-1').selected = true;
    } else{
        monthRadioOn.click();
        document.getElementById('schedule-select-month-'+input[0]).selected = true;
    }
    var result = document.getElementById('schedule_value');
    result.value = input[0] + 'Month ' + input[1] + 'Day ' + input[2] + 'Hour '  + input[3] + 'Minute';
}

function createCronLine(){
    var result = document.getElementById('schedule_value');
    if(minute != '*'){
        minute = document.getElementById('schedule-select-minute').value;
    }
    if(hour != '*'){
        hour = document.getElementById('schedule-select-hour').value;
    }
    if(day != '*'){
        day = document.getElementById('schedule-select-day').value;
    }
    if(month != '*'){
        month = document.getElementById('schedule-select-month').value;
    }
    result.value = month + 'Month ' + day + 'Day ' + hour + 'Hour '  + minute + 'Minute' ;
}

function schedule_init(){
    if(document.getElementById('schedulepro')){
        document.getElementById('schedule-select-minute').onchange = createCronLine;
        document.getElementById('schedule-select-hour').onchange = createCronLine;
        document.getElementById('schedule-select-day').onchange = createCronLine;
        document.getElementById('schedule-select-month').onchange = createCronLine;
        var received_value = document.getElementById('schedulepro').value;
        if(received_value != ''){
            setScheduleValue(document.getElementById('schedulepro').value);
        } else{
            setScheduleValue('*Month *Day *Hour *Minute');
        }
    }
}

window.onload = schedule_init;