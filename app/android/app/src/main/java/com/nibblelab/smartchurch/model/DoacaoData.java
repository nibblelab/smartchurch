package com.nibblelab.smartchurch.model;

import com.google.gson.annotations.SerializedName;

import java.util.List;

public class DoacaoData {
    @SerializedName("id")
    private String id;
    @SerializedName("nome")
    private String nome;

    public DoacaoData() {
    }

    public DoacaoData(String id, String nome) {
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

    public static String getNomeByIdInList(List<DoacaoData> list, String id)
    {
        for(DoacaoData l : list)
        {
            if(l.getId().equals(id))
            {
                return l.getNome();
            }
        }

        return "";
    }

    public static String getIdByNomeInList(List<DoacaoData> list, String nome)
    {
        for(DoacaoData d : list)
        {
            if(d.getNome().equals(nome))
            {
                return d.getId();
            }
        }

        return "";
    }
}
