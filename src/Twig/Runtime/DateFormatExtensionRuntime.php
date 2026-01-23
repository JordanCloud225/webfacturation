<?php

declare(strict_types=1);

namespace App\Twig\Runtime;

use DateTimeInterface;
use Twig\Extension\RuntimeExtensionInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class DateFormatExtensionRuntime implements RuntimeExtensionInterface
{
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * Retourne le format de la date traduite.
     *
     * @param object $date   date
     * @param string $format format de la date
     */
    public function dateFormat(object $date, string $format = 'l d F Y'): string
    {
        return str_replace(
            [date_format($date, 'l'), date_format($date, 'F')],
            [$this->translator->trans(strtoupper(date_format($date, 'l'))), $this->translator->trans(strtoupper(date_format($date, 'F')))],
            date_format($date, $format)
        );
    }

    public function stringToDateTime(string $date): DateTimeInterface
    {
        return new \DateTimeImmutable($date);
    }

    public function stringToDate(string $date, string $format): string
    {
        return date($format, strtotime($date));
    }

    /**
     * @param string $date1 '2012-08-15 16:01:05'
     * @param string $date2 '2012-08-14 16:01:05
     */
    public function dateDiff(string $date1, string $date2): string
    {
        $diff = abs(strtotime($date1) - strtotime($date2)); // abs pour avoir la valeur absolute, ainsi éviter d'avoir une différence négative
        $retour = [];

        $tmp = $diff;
        $retour['second'] = $tmp % 60;

        $tmp = floor(($tmp - $retour['second']) / 60);
        $retour['minute'] = $tmp % 60;

        $tmp = floor(($tmp - $retour['minute']) / 60);
        $retour['hour'] = $tmp % 24;

        $tmp = floor(($tmp - $retour['hour']) / 24);
        $retour['day'] = $tmp;

        return $retour['day'].' '.$this->translator->trans('DAYS');
    }

    public function removeDaysInDate(string $out, string $date, int $day): string
    {
        return date($out, strtotime($date.'- '.$day.' days'));
    }

    public function addDaysInDate(string $date, int $day): string
    {
        return date('d-m-Y', strtotime($date.'+ '.$day.' days'));
    }

    public function addMonthInDate(string $date, int $month): string
    {
        return date('d-m-Y', strtotime($date.'+ '.$month.' month'));
    }

    public function addYearInDate(string $date, int $year): string
    {
        return date('d-m-Y', strtotime($date.'+ '.$year.' year'));
    }
}