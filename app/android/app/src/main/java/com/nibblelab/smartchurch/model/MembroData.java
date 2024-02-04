package com.nibblelab.smartchurch.model;

import com.google.gson.annotations.SerializedName;

public class MembroData {

    @SerializedName("id")
    public String id;
    @SerializedName("pessoa")
    public String pessoa;
    @SerializedName("igreja")
    public String igreja;
    @SerializedName("codigo")
    public String codigo;
    @SerializedName("comungante")
    public boolean comungante;
    @SerializedName("especial")
    public boolean especial;
    @SerializedName("arrolado")
    public boolean arrolado;
    @SerializedName("data_admissao")
    public String data_admissao;
    @SerializedName("data_demissao")
    public String data_demissao;
    @SerializedName("stat")
    public String stat;
    @SerializedName("time_cad")
    public String time_cad;
    @SerializedName("last_mod")
    public String last_mod;
    @SerializedName("last_amod")
    public String last_amod;

    public MembroData() {
    }

    public MembroData(String id, String pessoa, String igreja, String codigo, boolean comungante,
                        boolean especial, boolean arrolado, String data_admissao, String data_demissao,
                        String stat, String time_cad, String last_mod, String last_amod) {
        this.id = id;
        this.pessoa = pessoa;
        this.igreja = igreja;
        this.codigo = codigo;
        this.comungante = comungante;
        this.especial = especial;
        this.arrolado = arrolado;
        this.data_admissao = data_admissao;
        this.data_demissao = data_demissao;
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

    public String getPessoa() {
        return pessoa;
    }

    public void setPessoa(String pessoa) {
        this.pessoa = pessoa;
    }

    public String getIgreja() {
        return igreja;
    }

    public void setIgreja(String igreja) {
        this.igreja = igreja;
    }

    public String getCodigo() {
        return codigo;
    }

    public void setCodigo(String codigo) {
        this.codigo = codigo;
    }

    public boolean isComungante() {
        return comungante;
    }

    public void setComungante(boolean comungante) {
        this.comungante = comungante;
    }

    public boolean isEspecial() {
        return especial;
    }

    public void setEspecial(boolean especial) {
        this.especial = especial;
    }

    public boolean isArrolado() {
        return arrolado;
    }

    public void setArrolado(boolean arrolado) {
        this.arrolado = arrolado;
    }

    public String getData_admissao() {
        return data_admissao;
    }

    public void setData_admissao(String data_admissao) {
        this.data_admissao = data_admissao;
    }

    public String getData_demissao() {
        return data_demissao;
    }

    public void setData_demissao(String data_demissao) {
        this.data_demissao = data_demissao;
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

}
