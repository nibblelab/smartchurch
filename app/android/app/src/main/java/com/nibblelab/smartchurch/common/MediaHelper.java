package com.nibblelab.smartchurch.common;

import java.util.regex.Matcher;
import java.util.regex.Pattern;

public class MediaHelper {

    public static String getYoutubeVideoId(String media)
    {
        String idVideo = "";

        if(!StringHelper.notEmpty(media)) {
            return "";
        }

        if(media.contains("youtube")) {
            // youtube
            Pattern pattern = Pattern.compile("youtube\\.com/watch\\?v=([\\da-zA-Z_\\-]*)", Pattern.CASE_INSENSITIVE);
            Matcher matcher = pattern.matcher(media);
            while (matcher.find()) {
                idVideo = matcher.group(1);
            }
        }
        else if(media.contains("youtu.be")) {
            // youtube reduzido
            Pattern pattern = Pattern.compile("youtu\\.be/([\\da-zA-Z_\\-]*)", Pattern.CASE_INSENSITIVE);
            Matcher matcher = pattern.matcher(media);
            while (matcher.find()) {
                idVideo = matcher.group(1);
            }
        }

        return idVideo;
    }

    public static String getVimeoVideoId(String media)
    {
        String idVideo = "";

        if(!StringHelper.notEmpty(media)) {
            return "";
        }

        if(media.contains("vimeo")) {
            // vimeo
            Pattern pattern = Pattern.compile("vimeo\\.com/([\\da-zA-Z_\\-]*)", Pattern.CASE_INSENSITIVE);
            Matcher matcher = pattern.matcher(media);
            while (matcher.find()) {
                idVideo = matcher.group(1);
            }
        }

        return idVideo;
    }

    public static String getSoundCloudAudioId(String media)
    {
        String idAudio = "";

        if(!StringHelper.notEmpty(media)) {
            return "";
        }

        if(media.contains("tracks")) {
            Pattern pattern = Pattern.compile("tracks/([\\da-zA-Z_\\-]*)", Pattern.CASE_INSENSITIVE);
            Matcher matcher = pattern.matcher(media);
            while (matcher.find()) {
                idAudio = matcher.group(1);
            }
        }

        return idAudio;
    }

    public static String getSoundCloudPlayerLink(String media)
    {
        String player = "";

        if(!StringHelper.notEmpty(media)) {
            return "";
        }

        if(media.contains("tracks")) {
            Pattern pattern = Pattern.compile("src=\"(.*)\"></", Pattern.CASE_INSENSITIVE);
            Matcher matcher = pattern.matcher(media);
            while (matcher.find()) {
                player = matcher.group(1);
            }
        }

        return player;
    }
}
