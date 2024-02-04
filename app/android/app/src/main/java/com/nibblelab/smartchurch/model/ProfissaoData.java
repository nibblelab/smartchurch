package com.nibblelab.smartchurch.model;

import com.google.gson.annotations.SerializedName;

import java.util.List;

public class ProfissaoData {
    @SerializedName("id")
    private String id;
    @SerializedName("nome")
    private String nome;

    public ProfissaoData() {
    }

    public ProfissaoData(String id, String nome) {
        this.id = id;
        this.nome = nome;
    }

    public String getId() {
        return id;
    }

    public void setId(String id) {
        this.id = id;
    }

    public String getNome() {
        return nome;
    }

    public void setNome(String nome) {
        this.nome = nome;
    }

    public static String getIdByNome(List<ProfissaoData> list, String nome)
    {
        for(ProfissaoData d : list)
        {
            if(d.getNome().equals(nome))
            {
                return d.getId();
            }
        }

        return "";
    }

    public static String getNomeById(List<ProfissaoData> list, String id)
    {
        for(ProfissaoData d : list)
        {
            if(d.getId().equals(id))
            {
                return d.getNome();
            }
        }

        return "";
    }
}
