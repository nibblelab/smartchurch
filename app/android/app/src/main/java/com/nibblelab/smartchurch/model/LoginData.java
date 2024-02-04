package com.nibblelab.smartchurch.model;

import com.google.gson.annotations.SerializedName;

public class LoginData {

    @SerializedName("email")
    private String email;
    @SerializedName("senha")
    private String senha;

    public LoginData() {
    }

    public LoginData(String email, String senha) {
        this.email = email;
        this.senha = senha;
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
