package com.nibblelab.smartchurch.model;

import com.google.gson.annotations.SerializedName;

import java.util.List;

public class DataListData {

    @SerializedName("sexo")
    private List<DataItemData> sexo;
    @SerializedName("estado_civil_void")
    private List<DataItemData> estado_civil;
    @SerializedName("escolaridade_void")
    private List<DataItemData> escolaridade;

    public DataListData() {
    }

    public DataListData(List<DataItemData> sexo, List<DataItemData> estado_civil, List<DataItemData> escolaridade) {
        this.sexo = sexo;
        this.estado_civil = estado_civil;
        this.escolaridade = escolaridade;
    }

    public List<DataItemData> getSexo() {
        return sexo;
    }

    public void setSexo(List<DataItemData> sexo) {
        this.sexo = sexo;
    }

    public List<DataItemData> getEstado_civil() {
        return estado_civil;
    }

    public void setEstado_civil(List<DataItemData> estado_civil) {
        this.estado_civil = estado_civil;
    }

    public List<DataItemData> getEscolaridade() {
        return escolaridade;
    }

    public void setEscolaridade(List<DataItemData> escolaridade) {
        this.escolaridade = escolaridade;
    }
}
