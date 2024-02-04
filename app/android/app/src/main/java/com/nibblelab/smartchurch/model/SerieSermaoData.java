package com.nibblelab.smartchurch.model;

import com.google.gson.annotations.SerializedName;

import java.util.List;

public class SerieSermaoData {
    @SerializedName("id")
    private String id;
    @SerializedName("igreja")
    private String igreja;
    @SerializedName("nome")
    private String nome;
    @SerializedName("chave")
    private String chave;
    @SerializedName("logo")
    private String logo;
    @SerializedName("stat")
    private String stat;
    @SerializedName("time_cad")
    private String time_cad;
    @SerializedName("last_mod")
    private String last_mod;
    @SerializedName("last_amod")
    private String last_amod;

    public SerieSermaoData() {
    }

    public SerieSermaoData(String id, String igreja, String nome, String chave, String logo,
                            String stat, String time_cad, String last_mod, String last_amod) {
        this.id = id;
        this.igreja = igreja;
        this.nome = nome;
        this.chave = chave;
        this.logo = logo;
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

    public static String getIdByNomeOnList(List<SerieSermaoData> list, String nome)
    {
        for(SerieSermaoData d: list)
        {
            if(d.getNome().equals(nome))
            {
                return d.getId();
            }
        }

        return "";
    }
}
