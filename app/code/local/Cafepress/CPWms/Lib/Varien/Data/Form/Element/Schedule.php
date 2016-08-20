<?php

class Cafepress_CPWms_Lib_Varien_Data_Form_Element_Schedule extends Varien_Data_Form_Element_Abstract
{
    public function __construct($attributes = array()){
        parent::__construct($attributes);
        $this->setType('label');
    }

    public function getElementHtml(){
        $html = '
            <table id="schedule_element">
                <tr>
                    <td class="schedule-header">Month</td>
                    <td class="schedule-header">Day</td>
                    <td class="schedule-header">Hour</td>
                    <td class="schedule-header">Minute</td>

                </tr>
                <tr>
                    <td>Every Month <input name="month" onclick="radioClicked(this)" type="radio" value="Every Month"></td>
                    <td>Every Day <input name="day" onclick="radioClicked(this)" type="radio" value="Every Day"></td>
                    <td>Every Hour <input name="hour" onclick="radioClicked(this)" type="radio" value="Every Hour"></td>
                    <td>Every Minute <input name="minute" onclick="radioClicked(this)" type="radio" value="Every Minute"></td>

                </tr>
                <tr>
                    <td>Choose <input name="month" onclick="radioClicked(this)" type="radio" value="Not Every Month"></td>
                    <td>Choose <input name="day" onclick="radioClicked(this)" type="radio" value="Not Every Day"></td>
                    <td>Choose <input name="hour" onclick="radioClicked(this)" type="radio" value="Not Every Hour"></td>
                    <td>Choose <input name="minute" onclick="radioClicked(this)" type="radio" value="Not Every Minute"></td>

                </tr>
                <tr>
                     <td>
                        <select name="month" id="schedule-select-month" class="schedule-select" multiple>
                            <option id="schedule-select-month-1" value="1">1</option>
                            <option id="schedule-select-month-2" value="2">2</option>
                            <option id="schedule-select-month-3" value="3">3</option>
                            <option id="schedule-select-month-4" value="4">4</option>
                            <option id="schedule-select-month-5" value="5">5</option>
                            <option id="schedule-select-month-6" value="6">6</option>
                            <option id="schedule-select-month-7" value="7">7</option>
                            <option id="schedule-select-month-8" value="8">8</option>
                            <option id="schedule-select-month-9" value="9">9</option>
                            <option id="schedule-select-month-10" value="10">10</option>
                            <option id="schedule-select-month-11" value="11">11</option>
                            <option id="schedule-select-month-12" value="12">12</option>
                        </select>
                    </td>

                    <td>
                        <select name="day" id="schedule-select-day" class="schedule-select" multiple>';
                            for($i = 1; $i < 32; $i++){
                                $html .= '<option id="schedule-select-day-'.$i.'" value="'.$i.'">'.$i.'</option>';
                            }
                            $html .= '</select>
                    </td>

                    <td>
                        <select name="hour" id="schedule-select-hour" class="schedule-select" multiple>';
                                for($i = 1; $i < 25; $i++){
                                    $html .= '<option id="schedule-select-hour-'.$i.'" value="'.$i.'">'.$i.'</option>';
                                }
                                $html .= '</select>
                    </td>

                    <td>
                        <select name="minute" id="schedule-select-minute" class="schedule-select" multiple>';
                            for($i = 1; $i < 61; $i++){
                                $html .= '<option id="schedule-select-minute-'.$i.'" value="'.$i.'">'.$i.'</option>';
                            }
                        $html .= '</select>
                    </td>

                </tr>
                <tr>
                    <td colspan="5">
                        <table width="100%"  cellpadding="0" cellspacing="0">
                            <tr>
                                <td><input id="schedule_value" type="text" name="xmlformat[schedulepro]" readonly="readonly" /></td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        ';
        return $html;
    }
}
