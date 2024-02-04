package com.nibblelab.smartchurch.common;

import org.apache.commons.lang3.StringUtils;

import java.text.DateFormat;
import java.text.DateFormatSymbols;
import java.text.ParseException;
import java.text.SimpleDateFormat;
import java.util.Calendar;
import java.util.Date;

public class DateHelper {

    /**
     * Converte de data no padrão SQL para o padrão de calendário
     *
     * @param date
     * @return
     */
    public static String fromDBDateToHumanDate(String date)
    {
        if(date == null || date.length() == 0) {
            return "";
        }

        try {
            Date dt = new SimpleDateFormat("yyyy-MM-dd").parse(date);
            DateFormat df = new SimpleDateFormat("dd/MM/yyyy");
            return df.format(dt);
        } catch (ParseException e) {
            e.printStackTrace();
        }

        return "";
    }

    /**
     * Converte data do padrão de calendário para o SQL
     *
     * @param date
     * @return
     */
    public static String fromHumamDateToDBDate(String date)
    {
        if(date == null || date.length() == 0) {
            return "";
        }

        try {
            Date dt = new SimpleDateFormat("dd/MM/yyyy").parse(date);
            DateFormat df = new SimpleDateFormat("yyyy-MM-dd");
            return df.format(dt);
        } catch (ParseException e) {
            e.printStackTrace();
        }

        return "";
    }

    /**
     * Converte de data e hora no padrão SQL para o padrão de calendário
     *
     * @param time
     * @return
     */
    public static String fromDBTimeToHumanTime(String time)
    {
        if(time == null || time.length() == 0) {
            return "";
        }

        try {
            Date dt = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss").parse(time);
            DateFormat df = new SimpleDateFormat("dd/MM/yyyy HH:mm:ss");
            return df.format(dt);
        } catch (ParseException e) {
            e.printStackTrace();
        }

        return "";
    }

    /**
     * Obtem a data em padrão de calendário a partir de date e hora no padrão SQL
     *
     * @param datetime
     * @return
     */
    public static String getHumanDateFromDBDateTime(String datetime)
    {
        if(datetime == null || datetime.length() == 0) {
            return "";
        }

        try {
            Date dt = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss").parse(datetime);
            DateFormat df = new SimpleDateFormat("dd/MM/yyyy");
            return df.format(dt);
        } catch (ParseException e) {
            e.printStackTrace();
        }

        return "";
    }

    /**
     * Converte data e hora do padrão de calendário para o SQL
     *
     * @param time
     * @return
     */
    public static String fromHumanTimeToDBTime(String time)
    {
        if(time == null || time.length() == 0) {
            return "";
        }

        try {
            Date dt = new SimpleDateFormat("dd/MM/yyyy HH:mm:ss").parse(time);
            DateFormat df = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss");
            return df.format(dt);
        } catch (ParseException e) {
            e.printStackTrace();
        }

        return "";
    }

    public static String fromDBTimeToHumanTime(String time, boolean time_only, boolean seconds)
    {
        if(time == null || time.length() == 0) {
            return "";
        }

        try {
            Date dt = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss").parse(time);
            String pattern = "dd/MM/yyyy HH:mm:ss";
            if(time_only) {
                pattern = (seconds) ? "HH:mm:ss" : "HH:mm";
            }
            DateFormat df = new SimpleDateFormat(pattern);
            return df.format(dt);
        } catch (ParseException e) {
            e.printStackTrace();
        }

        return "";
    }

    /**
     * Converte um objeto Date para String de data no padrão de calendário (DD/MM/YYYY)
     *
     * @param dt
     * @return
     */
    public static String fromDateToStringDate(Date dt)
    {
        if(dt == null) {
            return "";
        }

        try {
            DateFormat df = new SimpleDateFormat("dd/MM/yyyy");
            return df.format(dt);
        } catch (Exception e) {
            e.printStackTrace();
        }

        return "";
    }

    /**
     * Converte de data e hora no padrão SQL para objeto Date
     *
     * @param time
     * @return
     */
    public static Date fromDBTimeToDate(String time)
    {
        if(time == null || time.length() == 0) {
            return null;
        }

        try {
            Date dt = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss").parse(time);
            return dt;
        } catch (ParseException e) {
            e.printStackTrace();
        }

        return null;
    }

    /**
     * Converte de data no padrão do calendário para o objeto Date
     *
     * @param date
     * @return
     */
    public static Date fromHumanDateToDate(String date)
    {
        if(!StringHelper.notEmpty(date))
        {
            return null;
        }

        try {
            Date dt = new SimpleDateFormat("dd/MM/yyyy").parse(date);
            return dt;
        } catch (ParseException e) {
            e.printStackTrace();
        }

        return null;
    }

    /**
     * Converta de date para calendar
     *
     * @param dt date a ser convertido
     * @param resetTime flag para resetar a hora na data
     * @return
     */
    public static Calendar date2Calendar(Date dt, boolean resetTime)
    {
        Calendar cal = Calendar.getInstance();
        cal.setTime(dt);

        if(resetTime)
        {
            cal.set(Calendar.HOUR_OF_DAY, 0);
            cal.set(Calendar.MINUTE, 0);
            cal.set(Calendar.SECOND, 0);
            cal.set(Calendar.MILLISECOND, 0);
        }


        return cal;
    }

    /**
     * Converta de date para calendar
     *
     * @param dt
     * @return
     */
    public static Calendar date2Calendar(Date dt)
    {
        return DateHelper.date2Calendar(dt, false);
    }

    /**
     * Converte uma string no padrão de calendário para objeto Calendar
     *
     * @param date
     * @return
     */
    public static Calendar humanDate2Calendar(String date)
    {
        if(!StringHelper.notEmpty(date)) {
            return null;
        }

        Date dt = DateHelper.fromHumanDateToDate(date);
        if(dt != null)
        {
            Calendar cal = Calendar.getInstance();
            cal.setTime(dt);
            return cal;
        }

        return null;
    }

    /**
     * Obtêm a data (objeto Date) days dias atrás
     *
     * @param days
     * @return
     */
    public static Date dateFromDays(int days)
    {
        if(days < 0) {
            return new Date();
        }

        int d = -1 * days;
        Date dt = new Date();

        Calendar cal = DateHelper.date2Calendar(dt);
        cal.add(Calendar.DATE, d);
        return cal.getTime();
    }

    /**
     * Compara duas string de data no padrão de calendário
     *
     * @param date1
     * @param date2
     * @return
     */
    public static boolean equalDatesFromString(String date1, String date2)
    {
        if(date1 == null || date1.length() == 0 || date2 == null || date2.length() == 0) {
            return false;
        }

        try {
            Date dt1 = new SimpleDateFormat("dd/MM/yyyy").parse(date1);
            Date dt2 = new SimpleDateFormat("dd/MM/yyyy").parse(date2);

            Calendar cal1 = DateHelper.date2Calendar(dt1, true);
            Calendar cal2 = DateHelper.date2Calendar(dt2, true);

            return (cal1.get(Calendar.DAY_OF_YEAR) == cal2.get(Calendar.DAY_OF_YEAR) &&
                    cal1.get(Calendar.YEAR) == cal2.get(Calendar.YEAR));
        } catch (ParseException e) {
            e.printStackTrace();
        }

        return false;
    }

    public static boolean isBetweenDates(String date, String date_ini, String date_end)
    {
        if(!StringHelper.notEmpty(date) || !StringHelper.notEmpty(date_ini) || !StringHelper.notEmpty(date_end)) {
            return false;
        }

        try {
            Date dt = new SimpleDateFormat("dd/MM/yyyy").parse(date);
            Date dt_ini = new SimpleDateFormat("dd/MM/yyyy").parse(date_ini);
            Date dt_end = new SimpleDateFormat("dd/MM/yyyy").parse(date_end);

            return dt.compareTo(dt_ini) >= 0 && dt.compareTo(dt_end) <= 0;

        } catch (ParseException e) {
            e.printStackTrace();
        }

        return false;
    }

    /**
     * Obtem uma string do dia (sem zero)
     *
     * @param day
     * @return
     */
    public static String getDay(int day)
    {
        return getDay(day, false);
    }

    /**
     * Obtem uma string do dia
     *
     * @param day
     * @param leading
     * @return
     */
    public static String getDay(int day, boolean leading)
    {
        String r = "";
        if(leading) {
            if(day < 10) {
                r += "0";
            }
        }

        r += Integer.toString(day);

        return r;
    }

    /**
     * Obtem uma string do mês (sem zero)
     *
     * @param month
     * @return
     */
    public static String getMonth(int month)
    {
        return getMonth(month, false);
    }

    /**
     * Obtem uma string do mês
     *
     * @param month
     * @param leading
     * @return
     */
    public static String getMonth(int month, boolean leading)
    {
        String r = "";
        if(leading) {
            if(month < 10) {
                r += "0";
            }
        }

        r += Integer.toString(month);

        return r;
    }

    /**
     * Obtem uma string com o nome do mês
     *
     * @param month
     * @return
     */
    public static String getMonthName(int month)
    {
        String mes = new DateFormatSymbols().getMonths()[month-1];
        mes = StringUtils.capitalize(mes);

        return mes;
    }

    /**
     * Obtenha o nome do dia pelo seu número na semana
     *
     * @param day
     * @return
     */
    public static String getDayName(int day)
    {
        String dia = new DateFormatSymbols().getWeekdays()[day-1];

        return dia;
    }

    /**
     * Obtenha o nome do dia a partir da data no padrão de calendário
     *
     * @param date
     * @return
     */
    public static String getDayNameFromHumanDate(String date)
    {
        if(!StringHelper.notEmpty(date)) {
            return "";
        }

        String dia = "";
        Calendar cal = DateHelper.humanDate2Calendar(date);
        if(cal != null)
        {
            dia = DateHelper.getDayName(cal.get(Calendar.DAY_OF_WEEK) + 1);
        }

        return dia;
    }

    /**
     * Obtenha o dia a partir da data no padrão de calendário
     *
     * @param date
     * @return
     */
    public static String getDayFromHumanDate(String date)
    {
        if(!StringHelper.notEmpty(date)) {
            return "";
        }

        Calendar cal = DateHelper.humanDate2Calendar(date);
        if(cal != null)
        {
            return DateHelper.getDay(cal.get(Calendar.DAY_OF_MONTH), true);
        }

        return "";
    }

    /**
     * Obtenha o nome do mês a partir da data no padrão de calendário
     *
     * @param date
     * @return
     */
    public static String getMonthNameFromHumanDate(String date)
    {
        if(!StringHelper.notEmpty(date)) {
            return "";
        }

        Calendar cal = DateHelper.humanDate2Calendar(date);
        if(cal != null)
        {
            return DateHelper.getMonthName(cal.get(Calendar.MONTH) + 1);
        }

        return "";
    }

    /**
     * Obtenha o ano a partir da data no padrão de calendário
     *
     * @param date
     * @return
     */
    public static String getYearFromHumanDate(String date)
    {
        if(!StringHelper.notEmpty(date)) {
            return "";
        }

        Calendar cal = DateHelper.humanDate2Calendar(date);
        if(cal != null)
        {
            return DateHelper.getYear(cal.get(Calendar.YEAR));
        }

        return "";
    }

    /**
     *
     *
     * @param year
     * @return
     */
    public static String getYear(int year)
    {
        return Integer.toString(year);
    }

    /**
     * Gera uma data no padrão de calendário (DD/MM/YYYY) a partir dos inteiros referentes a dia, mês e ano
     *
     * @param day
     * @param month
     * @param year
     * @return
     */
    public static String getHumanDateFromInts(int day, int month, int year)
    {
        return getDay(day) + "/" + getMonth(month, true) + "/" + getYear(year);
    }

}
