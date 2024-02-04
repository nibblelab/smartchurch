package com.nibblelab.smartchurch.model;

import com.google.gson.annotations.SerializedName;

public class OficialData {
    @SerializedName("id")
    private String id;
    @SerializedName("nome")
    private String nome;
    @SerializedName("perfil")
    private String perfil;
    @SerializedName("data_nascimento")
    private String data_nascimento;
    @SerializedName("masculino")
    private boolean masculino;
    @SerializedName("feminino")
    private boolean feminino;
    @SerializedName("estado_civil")
    private String estado_civil;
    @SerializedName("escolaridade")
    private String escolaridade;
    @SerializedName("tem_filhos")
    private boolean tem_filhos;
    @SerializedName("pessoa")
    private String pessoa;
    @SerializedName("cargo")
    private String cargo;
    @SerializedName("diretoria")
    private String diretoria;
    @SerializedName("email")
    private String email;
    @SerializedName("telefone")
    private String telefone;
    @SerializedName("celular")
    private String celular;
    @SerializedName("inicio")
    private String inicio;
    @SerializedName("fim")
    private String fim;
    @SerializedName("ref")
    private String ref;
    @SerializedName("ref_tp")
    private String ref_tp;
    @SerializedName("stat")
    private String stat;
    @SerializedName("tipo")
    private String tipo;
    @SerializedName("disponibilidade")
    private String disponibilidade;
    @SerializedName("time_cad")
    private String time_cad;
    @SerializedName("last_mod")
    private String last_mod;
    @SerializedName("last_amod")
    private String last_amod;

    public OficialData() {
    }

    public OficialData(String id, String nome, String perfil, String data_nascimento, boolean masculino,
                       boolean feminino, String estado_civil, String escolaridade, boolean tem_filhos,
                       String pessoa, String cargo, String diretoria, String email, String telefone,
                       String celular, String inicio, String fim, String ref, String ref_tp,
                       String stat, String tipo, String disponibilidade, String time_cad,
                       String last_mod, String last_amod) {
        this.id = id;
        this.nome = nome;
        this.perfil = perfil;
        this.data_nascimento = data_nascimento;
        this.masculino = masculino;
        this.feminino = feminino;
        this.estado_civil = estado_civil;
        this.escolaridade = escolaridade;
        this.tem_filhos = tem_filhos;
        this.pessoa = pessoa;
        this.cargo = cargo;
        this.diretoria = diretoria;
        this.email = email;
        this.telefone = telefone;
        this.celular = celular;
        this.inicio = inicio;
        this.fim = fim;
        this.ref = ref;
        this.ref_tp = ref_tp;
        this.stat = stat;
        this.tipo = tipo;
        this.disponibilidade = disponibilidade;
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

    public String getNome() {
        return nome;
    }

    public void setNome(String nome) {
        this.nome = nome;
    }

    public String getPerfil() {
        return perfil;
    }

    public void setPerfil(String perfil) {
        this.perfil = perfil;
    }

    public String getData_nascimento() {
        return data_nascimento;
    }

    public void setData_nascimento(String data_nascimento) {
        this.data_nascimento = data_nascimento;
    }

    public boolean isMasculino() {
        return masculino;
    }

    public void setMasculino(boolean masculino) {
        this.masculino = masculino;
    }

    public boolean isFeminino() {
        return feminino;
    }

    public void setFeminino(boolean feminino) {
        this.feminino = feminino;
    }

    public String getEstado_civil() {
        return estado_civil;
    }

    public void setEstado_civil(String estado_civil) {
        this.estado_civil = estado_civil;
    }

    public String getEscolaridade() {
        return escolaridade;
    }

    public void setEscolaridade(String escolaridade) {
        this.escolaridade = escolaridade;
    }

    public boolean isTem_filhos() {
        return tem_filhos;
    }

    public void setTem_filhos(boolean tem_filhos) {
        this.tem_filhos = tem_filhos;
    }

    public String getPessoa() {
        return pessoa;
    }

    public void setPessoa(String pessoa) {
        this.pessoa = pessoa;
    }

    public String getCargo() {
        return cargo;
    }

    public void setCargo(String cargo) {
        this.cargo = cargo;
    }

    public String getDiretoria() {
        return diretoria;
    }

    public void setDiretoria(String diretoria) {
        this.diretoria = diretoria;
    }

    public String getEmail() {
        return email;
    }

    public void setEmail(String email) {
        this.email = email;
    }

    public String getTelefone() {
        return telefone;
    }

    public void setTelefone(String telefone) {
        this.telefone = telefone;
    }

    public String getCelular() {
        return celular;
    }

    public void setCelular(String celular) {
        this.celular = celular;
    }

    public String getInicio() {
        return inicio;
    }

    public void setInicio(String inicio) {
        this.inicio = inicio;
    }

    public String getFim() {
        return fim;
    }

    public void setFim(String fim) {
        this.fim = fim;
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

    public String getStat() {
        return stat;
    }

    public void setStat(String stat) {
        this.stat = stat;
    }

    public String getTipo() {
        return tipo;
    }

    public void setTipo(String tipo) {
        this.tipo = tipo;
    }

    public String getDisponibilidade() {
        return disponibilidade;
    }

    public void setDisponibilidade(String disponibilidade) {
        this.disponibilidade = disponibilidade;
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
