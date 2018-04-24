<?php

namespace HudhaifaS\Fields;

use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FormField;
use SilverStripe\Forms\NumericField;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\DataObjectInterface;
use SilverStripe\ORM\FieldType\DBField;
use SilverStripe\View\Requirements;
use Symfony\Component\Config\Tests\Util\Validator;

/**
 * 
 * @author Hudhaifa Shatnawi <hudhaifa.shatnawi@gmail.com>
 * @version 1.0, Sep 21, 2017 - 9:10:33 PM
 */
class GreatDateField
        extends FormField {

    /**
     * @var FormField
     */
    protected $fieldCalendar = null;

    /**
     * @var NumericField
     */
    protected $fieldYear = null;

    /**
     * @var NumericField
     */
    protected $fieldMonth = null;

    /**
     * @var NumericField
     */
    protected $fieldDay = null;

    public function __construct($name, $title = null, $value = "", $calendar = "Gregorian") {
        // fields
        // naming with underscores to prevent values from actually being saved somewhere
        $this->fieldDay = TextField::create("{$name}[Day]", false)
                ->addExtraClass('day fieldgroup-field')
                ->setAttribute('placeholder', _t('Date.FIELDLABELA_DAY', 'Day'))
                ->setMaxLength(2);

        $this->fieldMonth = TextField::create("{$name}[Month]", false)
                ->addExtraClass('month fieldgroup-field')
                ->setAttribute('placeholder', _t('Date.FIELDLABELA_MONTH', 'Month'))
                ->setMaxLength(2);
        $this->fieldYear = TextField::create("{$name}[Year]", false)
                ->addExtraClass('year fieldgroup-field')
                ->setAttribute('placeholder', _t('Date.FIELDLABELA_YEAR', 'Year'))
                ->setMaxLength(5);
        $this->fieldCalendar = DropdownField::create(
                        "{$name}[Calendar]", // 
                        _t('Date.FIELDLABELA_CALENDAR', 'Calendar'), // 
                        [
                    'Gregorian' => _t('Date.CALENDAR_GREGORIAN', 'G'),
                    'Hijri' => _t('Date.CALENDAR_HIJRI', 'H')
                        ], //
                        $calendar
                )
                ->addExtraClass('calendar fieldgroup-field');

        parent::__construct($name, $title, $value);
    }

    /**
     * @param array
     * @return HTMLText
     */
    public function Field($properties = []) {
        Requirements::css("hudhaifas/silverstripe-greatdate-field: res/css/greatdatefield.css");

        $format = 'd/m/y';
        $fields = [];

        $fields[stripos($format, 'd')] = $this->fieldDay->Field();
        $fields[stripos($format, 'm')] = $this->fieldMonth->Field();
        $fields[stripos($format, 'y')] = $this->fieldYear->Field();
        ksort($fields);
        $html = implode('/', $fields);
        $html .= " {$this->fieldCalendar->Field()}";

        return "<div class=\"controls\">{$html}</div>";
    }

    public function setValue($value, $data = null) {
        $this->value = $value;

        if (is_array($value)) {
//            $this->fieldYear->setValue(DBGreatDate::is_valid_year($val['Year']) ? $val['Year'] : null);
            $this->fieldYear->setValue($value['Year']);
            $this->fieldMonth->setValue(DBGreatDate::is_valid_month($value['Month']) ? $value['Month'] : null);
            $this->fieldDay->setValue(DBGreatDate::is_valid_day($value['Day']) ? $value['Day'] : null);
        } elseif ($value instanceof DBGreatDate) {
            $this->fieldYear->setValue(DBGreatDate::is_valid_year($value->getYear()) ? $value->getYear() : null);
            $this->fieldMonth->setValue(DBGreatDate::is_valid_month($value->getMonth()) ? $value->getMonth() : null);
            $this->fieldDay->setValue(DBGreatDate::is_valid_day($value->getDay()) ? $value->getDay() : null);
        }

//        $this->fieldCalendar->setValue('Gregorian');
        return $this;
    }

    public function saveInto(DataObjectInterface $dataObject) {
        $fieldName = $this->name;

        $yearValue = $this->fieldYear->dataValue();
        $monthValue = $this->fieldMonth->dataValue();
        $dayValue = $this->fieldDay->dataValue();

        $calendarValue = $this->fieldCalendar->dataValue();
        if ($calendarValue == 'Hijri') {
            $gregorian = HijriCalendar::hijriToGregorian($monthValue, $dayValue, $yearValue);

            list($monthValue, $dayValue, $yearValue) = $gregorian;
//            $monthValue = $gregorian[0];
//            $dayValue = $gregorian[1];
//            $yearValue = $gregorian[2];
        }

        if ($dataObject->hasMethod("set$fieldName")) {
            $dataObject->$fieldName = DBField::create_field('DBGreatDate', [
                        "Year" => $yearValue,
                        "Month" => $monthValue,
                        "Day" => $dayValue
            ]);
        } else {
            $dataObject->$fieldName->setYear($yearValue);
            $dataObject->$fieldName->setMonth($monthValue);
            $dataObject->$fieldName->setDay($dayValue);
        }
    }

    /**
     * Returns a readonly version of this field.
     */
    public function performReadonlyTransformation() {
        $clone = clone $this;

        $clone->fieldYear = $clone->fieldYear->performReadonlyTransformation();
        $clone->fieldMonth = $clone->fieldMonth->performReadonlyTransformation();
        $clone->fieldMonth = $clone->fieldMonth->performReadonlyTransformation();
        $clone->setReadonly(true);

        return $clone;
    }

    /**
     * @todo Implement removal of readonly state with $bool=false
     * @todo Set readonly state whenever field is recreated, e.g. in setAllowedCurrencies()
     */
    public function setReadonly($bool) {
        parent::setReadonly($bool);

        $this->fieldYear->setReadonly($bool);
        $this->fieldMonth->setReadonly($bool);
        $this->fieldDay->setReadonly($bool);
        $this->fieldCalendar->setReadonly($bool);

        return $this;
    }

    public function setDisabled($bool) {
        parent::setDisabled($bool);

        $this->fieldYear->setDisabled($bool);
        $this->fieldMonth->setDisabled($bool);
        $this->fieldDay->setDisabled($bool);
        $this->fieldCalendar->setDisabled($bool);

        return $this;
    }

    /**
     * Validate this field
     *
     * @param Validator $validator
     * @return bool
     */
    public function validate($validator) {
        return !DBGreatDate::is_valid_year($this->fieldYear);
    }

}
