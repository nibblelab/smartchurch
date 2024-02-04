package com.nibblelab.smartchurch.model;

import com.google.gson.annotations.SerializedName;
import com.nibblelab.smartchurch.common.StringHelper;

public class MembresiaData {

    @SerializedName("id")
    private String id;
    @SerializedName("igreja")
    private String igreja;
    @SerializedName("presbiterio")
    private String presbiterio;
    @SerializedName("sinodo")
    private String sinodo;
    @SerializedName("arrolado")
    private boolean arrolado;

    public MembresiaData() {
    }

    public MembresiaData(String id, String igreja, String presbiterio, String sinodo, boolean arrolado) {
        this.id = id;
        this.igreja = igreja;
        this.presbiterio = presbiterio;
        this.sinodo = sinodo;
        this.arrolado = arrolado;
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

    public String getPresbiterio() {
        return presbiterio;
    }

    public void setPresbiterio(String presbiterio) {
        this.presbiterio = presbiterio;
    }

    public String getSinodo() {
        return sinodo;
    }

    public void setSinodo(String sinodo) {
        this.sinodo = sinodo;
    }

    public boolean isArrolado() {
        return arrolado;
    }

    public void setArrolado(boolean arrolado) {
        this.arrolado = arrolado;
    }

    /**
     * Verifica se já informação de membresia
     *
     * @return
     */
    public boolean hasData()
    {
        return (StringHelper.notEmpty(this.id));
    }

    /**
     * Verifica se há informação de igreja
     *
     * @return
     */
    public boolean hasIgreja()
    {
        return StringHelper.notEmpty(this.igreja);
    }

    /**
     * Verifica se há informação de presbitério
     *
     * @return
     */
    public boolean hasPresbiterio()
    {
        return StringHelper.notEmpty(this.presbiterio);
    }

    /**
     * Verifica se há informação de sínodo
     *
     * @return
     */
    public boolean hasSinodo()
    {
        return StringHelper.notEmpty(this.sinodo);
    }
}
