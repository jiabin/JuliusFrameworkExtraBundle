<?php

/*
 * This file is part of the JuliusFrameworkExtraBundle package.
 *
 * (c) Eymen Gunay <eymen@egunay.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Julius\FrameworkExtraBundle\Form\Type;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;

use Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToStringTransformer;
use Symfony\Component\Form\AbstractType;

class DateTimePickerType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $emptyValue = $emptyValueDefault = function (Options $options) {
            return $options['required'] ? null : '';
        };

        $emptyValueNormalizer = function (Options $options, $emptyValue) use ($emptyValueDefault) {
            if (is_array($emptyValue)) {
                $default = $emptyValueDefault($options);

                return array_merge(
                    array('year' => $default, 'month' => $default, 'day' => $default),
                    $emptyValue
                );
            }

            return array(
                'year'  => $emptyValue,
                'month' => $emptyValue,
                'day'   => $emptyValue
            );
        };

        $resolver->setDefaults(array(
            // The date format, combination of p, P, h, hh, i, ii, s, ss, d, dd, m, mm, M, MM, yy, yyyy.
            //  p : meridian in lower case ('am' or 'pm') - according to locale file
            //  P : meridian in upper case ('AM' or 'PM') - according to locale file
            //  s : seconds without leading zeros
            //  ss : seconds, 2 digits with leading zeros
            //  i : minutes without leading zeros
            //  ii : minutes, 2 digits with leading zeros
            //  h : hour without leading zeros - 24-hour format
            //  hh : hour, 2 digits with leading zeros - 24-hour format
            //  H : hour without leading zeros - 12-hour format
            //  HH : hour, 2 digits with leading zeros - 12-hour format
            //  d : day of the month without leading zeros
            //  dd : day of the month, 2 digits with leading zeros
            //  m : numeric representation of month without leading zeros
            //  mm : numeric representation of the month, 2 digits with leading zeros
            //  M : short textual representation of a month, three letters
            //  MM : full textual representation of a month, such as January or March
            //  yy : two digit representation of a year
            //  yyyy : full numeric representation of a year, 4 digits
            'format'                => 'yyyy-mm-dd hh:ii',
            // Day of the week start. 0 (Sunday) to 6 (Saturday)
            'weekStart'             => 0,
            // Days of the week that should be disabled. Values are 0 (Sunday) to 6 (Saturday)
            'daysOfWeekDisabled'    => array(),
            // Whether or not to close the datetimepicker immediately when a date is selected.
            'autoclose'             => true,
            // The view that the datetimepicker should show when it is opened. Accepts values of :
            //  0 or 'hour' for the hour view
            //  1 or 'day' for the day view
            //  2 or 'month' for month view (the default)
            //  3 or 'year' for the 12-month overview
            //  4 or 'decade' for the 10-year overview. Useful for date-of-birth datetimepickers.
            'startView'             => 2,
            // The lowest view that the datetimepicker should show.
            'minView'               => 0,
            // The highest view that the datetimepicker should show.
            'maxView'               => 4,
            // If true or "linked", displays a "Today" button at the bottom of the datetimepicker to select the current date
            // If true, the "Today" button will only move the current date into view; if "linked", the current date will also be selected.
            'todayBtn'              => true,
            // If true, highlights the current date.
            'todayHighlight'        => true,
            // Whether or not to allow date navigation by arrow keys.
            'keyboardNavigation'    => true,
            // The two-letter code of the language to use for month and day names. 
            // These will also be used as the input's value (and subsequently sent to the server in the case of form submissions). 
            // Currently ships with English ('en'), German ('de'), Brazilian ('br'), and Spanish ('es') translations, 
            // but others can be added (see I18N below). If an unknown language code is given, English will be used.
            'language'              => 'en',
            // The increment used to build the hour view. A preset is created for each minuteStep minutes.
            'minuteStep'            => 5,
            // Allowed values are bottom-right and bottom-left
            'pickerPosition'        => 'bottom-right',
            // This option will enable meridian views for day and hour views.
            'showMeridian'          => false,
            // Empty value
            'empty_value'           => $emptyValue,
        ));

        $resolver->setNormalizers(array(
            'empty_value' => $emptyValueNormalizer,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $format = 'Y-m-d H:i';

        $builder->addViewTransformer(new DateTimeToStringTransformer(null, null, $format, false));
    }

    /**
     * {@inheritdoc}
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        parent::finishView($view, $form, $options);

        $view->vars['format']               = $options['format'];
        $view->vars['weekStart']            = $options['weekStart'];
        $view->vars['daysOfWeekDisabled']   = $options['daysOfWeekDisabled'];
        $view->vars['autoclose']            = $options['autoclose'] === true ? 'true' : 'false';
        $view->vars['startView']            = $options['startView'];
        $view->vars['minView']              = $options['minView'];
        $view->vars['maxView']              = $options['maxView'];
        $view->vars['todayBtn']             = $options['todayBtn'] === true ? 'true' : 'false';
        $view->vars['todayHighlight']       = $options['todayHighlight'] === true ? 'true' : 'false';
        $view->vars['keyboardNavigation']   = $options['keyboardNavigation'] === true ? 'true' : 'false';
        $view->vars['language']             = $options['language'];
        $view->vars['minuteStep']           = $options['minuteStep'];
        $view->vars['pickerPosition']       = $options['pickerPosition'];
        $view->vars['showMeridian']         = $options['showMeridian'] === true ? 'true' : 'false';
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'text';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'julius_datetimepicker';
    }
}
