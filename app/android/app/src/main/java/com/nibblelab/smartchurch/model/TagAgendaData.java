package com.nibblelab.smartchurch.model;

import android.graphics.Color;

import com.google.gson.annotations.SerializedName;
import com.nibblelab.smartchurch.common.StringHelper;

import java.util.List;

public class TagAgendaData {

    @SerializedName("id")
    private String id;
    @SerializedName("tag")
    private String tag;
    @SerializedName("cor")
    private String cor;
    @SerializedName("contextos")
    private String contextos;
    @SerializedName("stat")
    private String stat;
    @SerializedName("time_cad")
    private String time_cad;
    @SerializedName("last_mod")
    private String last_mod;
    @SerializedName("last_amod")
    private String last_amod;

    private int color;

    public TagAgendaData() {
    }

    public TagAgendaData(String id, String tag, String cor, String contextos, String stat,
                         String time_cad, String last_mod, String last_amod) {
        this.id = id;
        this.tag = tag;
        this.cor = cor;
        this.contextos = contextos;
        this.stat = stat;
        this.time_cad = time_cad;
        this.last_mod = last_mod;
        this.last_amod = last_amod;
    }

    public String getId() {
        return id;
    }

    public void setId(String id) {
        this.id = id;
    }

    public String getTag() {
        return tag;
    }

    public void setTag(String tag) {
        this.tag = tag;
    }

    public String getCor() {
        return cor;
    }

    public void setCor(String cor) {
        this.cor = cor;
    }

    public String getContextos() {
        return contextos;
    }

    public void setContextos(String contextos) {
        this.contextos = contextos;
    }

    public String getStat() {
        return stat;
    }

    public void setStat(String stat) {
        this.stat = stat;
    }

    public String getTime_cad() {
        return time_cad;
    }

    public void setTime_cad(String time_cad) {
        this.time_cad = time_cad;
    }

    public String getLast_mod() {
        return last_mod;
    }

    public void setLast_mod(String last_mod) {
        this.last_mod = last_mod;
    }

    public String getLast_amod() {
        return last_amod;
    }

    public void setLast_amod(String last_amod) {
        this.last_amod = last_amod;
    }

    public int getColor()
    {
        return this.color;
    }

    public void generateColor()
    {
        if(StringHelper.notEmpty(this.cor))
        {
            String c = this.cor;
            c = c.replace("hsl(", "");
            c = c.replace(")", "");

            String[] c_v = c.split(",");

            String hue_str = c_v[0].trim();
            String saturation_str = c_v[1].trim().replace("%", "");
            String lightness_str = c_v[2].trim().replace("%", "");

            Float hue = Float.parseFloat(hue_str);
            Float saturation = Float.parseFloat(saturation_str)/100;
            Float lightness = Float.parseFloat(lightness_str)/100;

            this.color = Color.HSVToColor( new float[]{ hue, saturation , lightness } );
        }
    }

}
