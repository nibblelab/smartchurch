package com.nibblelab.smartchurch.model;

import com.google.gson.annotations.SerializedName;

import java.util.List;

public class TemploData {

    @SerializedName("id")
    private String id;
    @SerializedName("sinodo")
    private String sinodo;
    @SerializedName("presbiterio")
    private String presbiterio;
    @SerializedName("igreja")
    private String igreja;
    @SerializedName("nome")
    private String nome;
    @SerializedName("fundacao")
    private String fundacao;
    @SerializedName("telefone")
    private String telefone;
    @SerializedName("email")
    private String email;
    @SerializedName("endereco")
    private String endereco;
    @SerializedName("numero")
    private String numero;
    @SerializedName("complemento")
    private String complemento;
    @SerializedName("bairro")
    private String bairro;
    @SerializedName("cidade")
    private String cidade;
    @SerializedName("uf")
    private String uf;
    @SerializedName("cep")
    private String cep;
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
    @SerializedName("stat")
    private String stat;
    @SerializedName("time_cad")
    private String time_cad;
    @SerializedName("last_mod")
    private String last_mod;
    @SerializedName("last_amod")
    private String last_amod;

    public TemploData() {
    }

    public TemploData(String id, String sinodo, String presbiterio, String igreja, String nome,
                        String fundacao, String telefone, String email, String endereco, String numero,
                        String complemento, String bairro, String cidade, String uf, String cep,
                        String site, String facebook, String instagram, String youtube, String vimeo,
                        String stat, String time_cad, String last_mod, String last_amod) {
        this.id = id;
        this.sinodo = sinodo;
        this.presbiterio = presbiterio;
        this.igreja = igreja;
        this.nome = nome;
        this.fundacao = fundacao;
        this.telefone = telefone;
        this.email = email;
        this.endereco = endereco;
        this.numero = numero;
        this.complemento = complemento;
        this.bairro = bairro;
        this.cidade = cidade;
        this.uf = uf;
        this.cep = cep;
        this.site = site;
        this.facebook = facebook;
        this.instagram = instagram;
        this.youtube = youtube;
        this.vimeo = vimeo;
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

    public String getSinodo() {
        return sinodo;
    }

    public void setSinodo(String sinodo) {
        this.sinodo = sinodo;
    }

    public String getPresbiterio() {
        return presbiterio;
    }

    public void setPresbiterio(String presbiterio) {
        this.presbiterio = presbiterio;
    }

    public String getIgreja() {
        return igreja;
    }

    public void setIgreja(String igreja) {
        this.igreja = igreja;
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

    public String getTelefone() {
        return telefone;
    }

    public void setTelefone(String telefone) {
        this.telefone = telefone;
    }

    public String getEmail() {
        return email;
    }

    public void setEmail(String email) {
        this.email = email;
    }

    public String getEndereco() {
        return endereco;
    }

    public void setEndereco(String endereco) {
        this.endereco = endereco;
    }

    public String getNumero() {
        return numero;
    }

    public void setNumero(String numero) {
        this.numero = numero;
    }

    public String getComplemento() {
        return complemento;
    }

    public void setComplemento(String complemento) {
        this.complemento = complemento;
    }

    public String getBairro() {
        return bairro;
    }

    public void setBairro(String bairro) {
        this.bairro = bairro;
    }

    public String getCidade() {
        return cidade;
    }

    public void setCidade(String cidade) {
        this.cidade = cidade;
    }

    public String getUf() {
        return uf;
    }

    public void setUf(String uf) {
        this.uf = uf;
    }

    public String getCep() {
        return cep;
    }

    public void setCep(String cep) {
        this.cep = cep;
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

    public static String getIdByNomeOnList(List<TemploData> list, String nome)
    {
        for(TemploData d: list)
        {
            if(d.getNome().equals(nome))
            {
                return d.getId();
            }
        }

        return "";
    }
}
