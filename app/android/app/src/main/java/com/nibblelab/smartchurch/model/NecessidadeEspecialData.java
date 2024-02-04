package com.nibblelab.smartchurch.model;

import com.google.gson.annotations.SerializedName;

import java.util.List;

public class NecessidadeEspecialData {
    @SerializedName("id")
    private String id;
    @SerializedName("nome")
    private String nome;

    public NecessidadeEspecialData() {
    }

    public NecessidadeEspecialData(String id, String nome) {
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

    public static String getNomeByIdInList(List<NecessidadeEspecialData> list, String id)
    {
        for(NecessidadeEspecialData l : list)
        {
            if(l.getId().equals(id))
            {
                return l.getNome();
            }
        }

        return "";
    }

    public static String getIdByNomeInList(List<NecessidadeEspecialData> list, String nome)
    {
        for(NecessidadeEspecialData l : list)
        {
            if(l.getNome().equals(nome))
            {
                return l.getId();
            }
        }

        return "";
    }
}
