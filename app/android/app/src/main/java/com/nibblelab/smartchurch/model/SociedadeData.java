package com.nibblelab.smartchurch.model;

import com.google.gson.annotations.SerializedName;

public class SociedadeData {
    @SerializedName("id")
    private String id;
    @SerializedName("igreja")
    private String igreja;
    @SerializedName("federacao")
    private String federacao;
    @SerializedName("sinodal")
    private String sinodal;
    @SerializedName("nacional")
    private String nacional;
    @SerializedName("reference")
    private String reference;
    @SerializedName("nome")
    private String nome;
    @SerializedName("logo")
    private String logo;
    @SerializedName("fundacao")
    private String fundacao;
    @SerializedName("email")
    private String email;
    @SerializedName("telefone")
    private String telefone;
    @SerializedName("ramal")
    private String ramal;
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

    public SociedadeData() {
    }

    public SociedadeData(String id, String igreja, String federacao, String sinodal, String nacional,
                         String reference, String nome, String logo, String fundacao, String email,
                         String telefone, String ramal, String stat, String site, String facebook,
                         String instagram, String youtube, String vimeo, String time_cad,
                         String last_mod, String last_amod) {
        this.id = id;
        this.igreja = igreja;
        this.federacao = federacao;
        this.sinodal = sinodal;
        this.nacional = nacional;
        this.reference = reference;
        this.nome = nome;
        this.logo = logo;
        this.fundacao = fundacao;
        this.email = email;
        this.telefone = telefone;
        this.ramal = ramal;
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

    public String getIgreja() {
        return igreja;
    }

    public void setIgreja(String igreja) {
        this.igreja = igreja;
    }

    public String getFederacao() {
        return federacao;
    }

    public void setFederacao(String federacao) {
        this.federacao = federacao;
    }

    public String getSinodal() {
        return sinodal;
    }

    public void setSinodal(String sinodal) {
        this.sinodal = sinodal;
    }

    public String getNacional() {
        return nacional;
    }

    public void setNacional(String nacional) {
        this.nacional = nacional;
    }

    public String getReference() {
        return reference;
    }

    public void setReference(String reference) {
        this.reference = reference;
    }

    public String getNome() {
        return nome;
    }

    public void setNome(String nome) {
        this.nome = nome;
    }

    public String getLogo() {
        return logo;
    }

    public void setLogo(String logo) {
        this.logo = logo;
    }

    public String getFundacao() {
        return fundacao;
    }

    public void setFundacao(String fundacao) {
        this.fundacao = fundacao;
    }

    public String getEmail() {
        return email;
    }

    public void setEmail(String email) {
        this.email = email;
    }

    public String getTelefone() {
        return telefone;
    }

    public void setTelefone(String telefone) {
        this.telefone = telefone;
    }

    public String getRamal() {
        return ramal;
    }

    public void setRamal(String ramal) {
        this.ramal = ramal;
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
}