package com.nibblelab.smartchurch.model;

import com.google.gson.annotations.SerializedName;

public class PermsData {

    @SerializedName("id")
    private String id;
    @SerializedName("nome")
    private String nome;
    @SerializedName("modulo")
    private String modulo;

    public PermsData() {
    }

    public PermsData(String id, String nome, String modulo) {
        this.id = id;
        this.nome = nome;
        this.modulo = modulo;
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

    public String getModulo() {
        return modulo;
    }

    public void setModulo(String modulo) {
        this.modulo = modulo;
    }
}
