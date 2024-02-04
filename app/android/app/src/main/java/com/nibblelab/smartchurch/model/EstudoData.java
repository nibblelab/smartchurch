package com.nibblelab.smartchurch.model;

import com.google.gson.annotations.SerializedName;

public class EstudoData {
    @SerializedName("id")
    private String id;
    @SerializedName("ref")
    private String ref;
    @SerializedName("ref_tp")
    private String ref_tp;
    @SerializedName("serie")
    private String serie;
    @SerializedName("autor")
    private String autor;
    @SerializedName("titulo")
    private String titulo;
    @SerializedName("chave")
    private String chave;
    @SerializedName("logo")
    private String logo;
    @SerializedName("logo_app")
    private String logo_app;
    @SerializedName("conteudo")
    private String conteudo;
    @SerializedName("anexo")
    private String anexo;
    @SerializedName("video")
    private String video;
    @SerializedName("audio")
    private String audio;
    @SerializedName("stat")
    private String stat;
    @SerializedName("time_cad")
    private String time_cad;
    @SerializedName("last_mod")
    private String last_mod;
    @SerializedName("last_amod")
    private String last_amod;

    public EstudoData() {}

    public EstudoData(String id, String ref, String ref_tp, String serie, String autor, String titulo,
                      String chave, String logo, String logo_app, String conteudo, String anexo, String video,
                      String audio, String stat, String time_cad, String last_mod, String last_amod) {
        this.id = id;
        this.ref = ref;
        this.ref_tp = ref_tp;
        this.serie = serie;
        this.autor = autor;
        this.titulo = titulo;
        this.chave = chave;
        this.logo = logo;
        this.logo_app = logo_app;
        this.conteudo = conteudo;
        this.anexo = anexo;
        this.video = video;
        this.audio = audio;
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

    public String getSerie() {
        return serie;
    }

    public void setSerie(String serie) {
        this.serie = serie;
    }

    public String getAutor() {
        return autor;
    }

    public void setAutor(String autor) {
        this.autor = autor;
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

    public String getLogo() {
        return logo;
    }

    public void setLogo(String logo) {
        this.logo = logo;
    }

    public String getLogoApp() {
        return logo_app;
    }

    public void setLogoApp(String logo_app) {
        this.logo_app = logo_app;
    }

    public String getConteudo() {
        return conteudo;
    }

    public void setConteudo(String conteudo) {
        this.conteudo = conteudo;
    }

    public String getAnexo() {
        return anexo;
    }

    public void setAnexo(String anexo) {
        this.anexo = anexo;
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
