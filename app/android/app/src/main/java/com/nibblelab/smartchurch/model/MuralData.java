package com.nibblelab.smartchurch.model;

import com.google.gson.annotations.SerializedName;

public class MuralData {
    @SerializedName("id")
    private String id;
    @SerializedName("ref")
    private String ref;
    @SerializedName("ref_tp")
    private String ref_tp;
    @SerializedName("titulo")
    private String titulo;
    @SerializedName("chave")
    private String chave;
    @SerializedName("img")
    private String img;
    @SerializedName("conteudo")
    private String conteudo;
    @SerializedName("video")
    private String video;
    @SerializedName("audio")
    private String audio;
    @SerializedName("stat")
    private String stat;
    @SerializedName("marked")
    private boolean marked;
    @SerializedName("mark_id")
    private String mark_id;
    @SerializedName("time_cad")
    private String time_cad;
    @SerializedName("last_mod")
    private String last_mod;
    @SerializedName("last_amod")
    private String last_amod;

    public MuralData() {
    }

    public MuralData(String id, String ref, String ref_tp, String titulo, String chave, String img,
                     String conteudo, String video, String audio, String stat, boolean marked,
                     String mark_id, String time_cad, String last_mod, String last_amod) {
        this.id = id;
        this.ref = ref;
        this.ref_tp = ref_tp;
        this.titulo = titulo;
        this.chave = chave;
        this.img = img;
        this.conteudo = conteudo;
        this.video = video;
        this.audio = audio;
        this.stat = stat;
        this.marked = marked;
        this.mark_id = mark_id;
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

    public String getTitulo() {
        return titulo;
    }

    public void setTitulo(String titulo) {
        this.titulo = titulo;
    }

    public String getChave() {
        return chave;
    }

    public void setChave(String chave) {
        this.chave = chave;
    }

    public String getImg() {
        return img;
    }

    public void setImg(String img) {
        this.img = img;
    }

    public String getConteudo() {
        return conteudo;
    }

    public void setConteudo(String conteudo) {
        this.conteudo = conteudo;
    }

    public String getVideo() {
        return video;
    }

    public void setVideo(String video) {
        this.video = video;
    }

    public String getAudio() {
        return audio;
    }

    public void setAudio(String audio) {
        this.audio = audio;
    }

    public String getStat() {
        return stat;
    }

    public void setStat(String stat) {
        this.stat = stat;
    }

    public boolean isMarked() {
        return marked;
    }

    public void setMarked(boolean marked) {
        this.marked = marked;
    }

    public String getMark_id() {
        return mark_id;
    }

    public void setMark_id(String mark_id) {
        this.mark_id = mark_id;
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
