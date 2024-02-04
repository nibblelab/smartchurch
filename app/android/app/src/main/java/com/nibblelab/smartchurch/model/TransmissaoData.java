package com.nibblelab.smartchurch.model;

import com.google.gson.annotations.SerializedName;

public class TransmissaoData {
    @SerializedName("id")
    private String id;
    @SerializedName("ref")
    private String ref;
    @SerializedName("ref_tp")
    private String ref_tp;
    @SerializedName("video")
    private String video;
    @SerializedName("stat")
    private String stat;
    @SerializedName("time_cad")
    private String time_cad;
    @SerializedName("last_mod")
    private String last_mod;
    @SerializedName("last_amod")
    private String last_amod;

    public TransmissaoData() {
    }

    public TransmissaoData(String id, String ref, String ref_tp, String video, String stat, String time_cad, String last_mod, String last_amod) {
        this.id = id;
        this.ref = ref;
        this.ref_tp = ref_tp;
        this.video = video;
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

    public String getRef() {
        return ref;
    }

    public void setRef(String ref) {
        this.ref = ref;
    }

    public String getRef_tp() {
        return ref_tp;
    }

    public void setRef_tp(String ref_tp) {
        this.ref_tp = ref_tp;
    }

    public String getVideo() {
        return video;
    }

    public void setVideo(String video) {
        this.video = video;
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
}
