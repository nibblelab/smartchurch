package com.nibblelab.smartchurch.model;

import com.google.gson.annotations.SerializedName;

import java.util.List;

public class SinodoData {

    @SerializedName("id")
    private String id;
    @SerializedName("nacional")
    private String nacional;
    @SerializedName("sigla")
    private String sigla;
    @SerializedName("nome")
    private String nome;
    @SerializedName("fundacao")
    private String fundacao;
    @SerializedName("localidades")
    private String localidades;
    @SerializedName("stat")
    private String stat;
    @SerializedName("site")
    private String site;
    @SerializedName("facebook")
    private String facebook;
    @SerializedName("instagram")
    private String instagram;
    @SerializedName("youtube")
    private String youtube;
    @SerializedName("vimeo")
    private String vimeo;
    @SerializedName("time_cad")
    private String time_cad;
    @SerializedName("last_mod")
    private String last_mod;
    @SerializedName("last_amod")
    private String last_amod;

    public SinodoData() {
    }

    public SinodoData(String id, String nacional, String sigla, String nome, String fundacao, String localidades,
                      String stat, String site, String facebook, String instagram, String youtube,
                      String vimeo, String time_cad, String last_mod, String last_amod) {
        this.id = id;
        this.nacional = nacional;
        this.sigla = sigla;
        this.nome = nome;
        this.fundacao = fundacao;
        this.localidades = localidades;
        this.stat = stat;
        this.site = site;
        this.facebook = facebook;
        this.instagram = instagram;
        this.youtube = youtube;
        this.vimeo = vimeo;
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

    public String getNacional() {
        return nacional;
    }

    public void setNacional(String nacional) {
        this.nacional = nacional;
    }

    public String getSigla() {
        return sigla;
    }

    public void setSigla(String sigla) {
        this.sigla = sigla;
    }

    public String getNome() {
        return nome;
    }

    public void setNome(String nome) {
        this.nome = nome;
    }

    public String getFundacao() {
        return fundacao;
    }

    public void setFundacao(String fundacao) {
        this.fundacao = fundacao;
    }

    public String getLocalidades() {
        return localidades;
    }

    public void setLocalidades(String localidades) {
        this.localidades = localidades;
    }

    public String getStat() {
        return stat;
    }

    public void setStat(String stat) {
        this.stat = stat;
    }

    public String getSite() {
        return site;
    }

    public void setSite(String site) {
        this.site = site;
    }

    public String getFacebook() {
        return facebook;
    }

    public void setFacebook(String facebook) {
        this.facebook = facebook;
    }

    public String getInstagram() {
        return instagram;
    }

    public void setInstagram(String instagram) {
        this.instagram = instagram;
    }

    public String getYoutube() {
        return youtube;
    }

    public void setYoutube(String youtube) {
        this.youtube = youtube;
    }

    public String getVimeo() {
        return vimeo;
    }

    public void setVimeo(String vimeo) {
        this.vimeo = vimeo;
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

    public static String getIdByNomeOnList(List<SinodoData> list, String nome)
    {
        for(SinodoData d: list)
        {
            if(d.getNome().equals(nome))
            {
                return d.getId();
            }
        }

        return "";
    }

}
