package com.nibblelab.smartchurch.model;

import com.google.gson.annotations.SerializedName;

public class RegisterData {

    @SerializedName("nome")
    private String nome;
    @SerializedName("email")
    private String email;
    @SerializedName("senha")
    private String senha;

    public RegisterData() {
    }

    public RegisterData(String nome, String email, String senha) {
        this.nome = nome;
        this.email = email;
        this.senha = senha;
    }

    public String getNome() {
        return nome;
    }

    public void setNome(String nome) {
        this.nome = nome;
    }

    public String getEmail() {
        return email;
    }

    public void setEmail(String email) {
        this.email = email;
    }

    public String getSenha() {
        return senha;
    }

    public void setSenha(String senha) {
        this.senha = senha;
    }
}
