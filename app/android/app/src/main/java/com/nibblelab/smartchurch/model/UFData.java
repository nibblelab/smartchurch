package com.nibblelab.smartchurch.model;

import com.google.gson.annotations.SerializedName;

import java.util.List;

public class UFData {

    @SerializedName("id")
    private int id;
    @SerializedName("sigla")
    private String sigla;
    @SerializedName("nome")
    private String nome;

    public UFData() {
    }

    public UFData(int id, String sigla, String nome) {
        this.id = id;
        this.sigla = sigla;
        this.nome = nome;
    }

    public int getId() {
        return id;
    }

    public void setId(int id) {
        this.id = id;
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

    public static String getNomeBySigla(List<UFData> list, String sigla)
    {
        for(UFData d : list)
        {
            if(d.getSigla().equals(sigla)) {
                return d.getNome();
            }
        }

        return "";
    }
}
