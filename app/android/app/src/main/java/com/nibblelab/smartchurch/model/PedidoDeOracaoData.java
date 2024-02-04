package com.nibblelab.smartchurch.model;

import com.google.gson.annotations.SerializedName;

public class PedidoDeOracaoData {
    @SerializedName("id")
    private String id;
    @SerializedName("autor")
    private String autor;
    @SerializedName("autor_nome")
    private String autorNome;
    @SerializedName("igreja")
    private String igreja;
    @SerializedName("ref")
    private String ref;
    @SerializedName("ref_tp")
    private String ref_tp;
    @SerializedName("pedido")
    private String pedido;
    @SerializedName("stat")
    private String stat;
    @SerializedName("time_cad")
    private String time_cad;
    @SerializedName("last_mod")
    private String last_mod;
    @SerializedName("last_amod")
    private String last_amod;

    public PedidoDeOracaoData() {}

    public PedidoDeOracaoData(String id, String autor, String autorNome, String igreja, String ref,
                              String ref_tp, String pedido,
                              String stat, String time_cad, String last_mod, String last_amod) {
        this.id = id;
        this.autor = autor;
        this.autorNome = autorNome;
        this.igreja = igreja;
        this.ref = ref;
        this.ref_tp = ref_tp;
        this.pedido = pedido;
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

    public String getAutor() {
        return autor;
    }

    public void setAutor(String autor) {
        this.autor = autor;
    }

    public String getAutorNome() {
        return autorNome;
    }

    public void setAutorNome(String autorNome) {
        this.autorNome = autorNome;
    }

    public String getIgreja() {
        return igreja;
    }

    public void setIgreja(String igreja) {
        this.igreja = igreja;
    }

    public String getRef() {
        return ref;
    }

    public void setRef(String ref) {
        this.ref = ref;
    }

    public String getRef_tp() {
        return ref_tp;
    }

    public void setRef_tp(String ref_tp) {
        this.ref_tp = ref_tp;
    }

    public String getPedido() {
        return pedido;
    }

    public void setPedido(String pedido) {
        this.pedido = pedido;
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
