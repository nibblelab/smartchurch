package com.nibblelab.smartchurch.model;

import com.google.gson.annotations.SerializedName;

import java.util.List;

public class PessoaData<T> {

    @SerializedName("id")
    protected String id;
    @SerializedName("perfil")
    protected String perfil;
    @SerializedName("nome")
    protected String nome;
    @SerializedName("email")
    protected String email;
    @SerializedName("senha")
    protected String senha;
    @SerializedName("sexo")
    protected String sexo;
    @SerializedName("data_nascimento")
    protected String data_nascimento;
    @SerializedName("estado_civil")
    protected String estado_civil;
    @SerializedName("tem_filhos")
    protected boolean tem_filhos;
    @SerializedName("escolaridade")
    protected String escolaridade;
    @SerializedName("profissao")
    protected String profissao;
    @SerializedName("endereco")
    protected String endereco;
    @SerializedName("numero")
    protected String numero;
    @SerializedName("complemento")
    protected String complemento;
    @SerializedName("bairro")
    protected String bairro;
    @SerializedName("cidade")
    protected String cidade;
    @SerializedName("uf")
    protected String uf;
    @SerializedName("cep")
    protected String cep;
    @SerializedName("telefone")
    protected String telefone;
    @SerializedName("celular_1")
    protected String celular_1;
    @SerializedName("celular_2")
    protected String celular_2;
    @SerializedName("doacoes")
    protected List<T> doacoes;
    @SerializedName("necessidades_especiais")
    protected List<T> necessidades_especiais;
    @SerializedName("site")
    protected String site;
    @SerializedName("facebook")
    protected String facebook;
    @SerializedName("instagram")
    protected String instagram;
    @SerializedName("youtube")
    protected String youtube;
    @SerializedName("vimeo")
    protected String vimeo;

    public PessoaData() {
    }

    public PessoaData(String id, String perfil, String nome, String email, String senha, String sexo,
                      String data_nascimento, String estado_civil, boolean tem_filhos, String escolaridade, String profissao,
                      String endereco, String numero, String complemento, String bairro, String cidade,
                      String uf, String cep, String telefone, String celular_1, String celular_2,
                      List<T> doacoes, List<T> necessidades_especiais, String site, String facebook,
                      String instagram, String youtube, String vimeo) {
        this.id = id;
        this.perfil = perfil;
        this.nome = nome;
        this.email = email;
        this.senha = senha;
        this.sexo = sexo;
        this.data_nascimento = data_nascimento;
        this.estado_civil = estado_civil;
        this.tem_filhos = tem_filhos;
        this.escolaridade = escolaridade;
        this.profissao = profissao;
        this.endereco = endereco;
        this.numero = numero;
        this.complemento = complemento;
        this.bairro = bairro;
        this.cidade = cidade;
        this.uf = uf;
        this.cep = cep;
        this.telefone = telefone;
        this.celular_1 = celular_1;
        this.celular_2 = celular_2;
        this.doacoes = doacoes;
        this.necessidades_especiais = necessidades_especiais;
        this.site = site;
        this.facebook = facebook;
        this.instagram = instagram;
        this.youtube = youtube;
        this.vimeo = vimeo;
    }

    public void load(PessoaData<T> cp)
    {
        this.id = cp.getId();
        this.perfil = cp.getPerfil();
        this.nome = cp.getNome();
        this.email = cp.getEmail();
        this.senha = cp.getSenha();
        this.sexo = cp.getSexo();
        this.data_nascimento = cp.getData_nascimento();
        this.estado_civil = cp.getEstado_civil();
        this.tem_filhos = cp.getTem_filhos();
        this.escolaridade = cp.getEscolaridade();
        this.profissao = cp.getProfissao();
        this.endereco = cp.getEndereco();
        this.numero = cp.getNumero();
        this.complemento = cp.getComplemento();
        this.bairro = cp.getBairro();
        this.cidade = cp.getCidade();
        this.uf = cp.getUf();
        this.cep = cp.getCep();
        this.telefone = cp.getTelefone();
        this.celular_1 = cp.getCelular_1();
        this.celular_2 = cp.getCelular_2();
        this.doacoes = cp.getDoacoes();
        this.necessidades_especiais = cp.getNecessidades_especiais();
    }

    public String getId() {
        return id;
    }

    public void setId(String id) {
        this.id = id;
    }

    public String getPerfil() {
        return perfil;
    }

    public void setPerfil(String perfil) {
        this.perfil = perfil;
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

    public String getSexo() {
        return sexo;
    }

    public void setSexo(String sexo) {
        this.sexo = sexo;
    }

    public String getData_nascimento() {
        return data_nascimento;
    }

    public void setData_nascimento(String data_nascimento) {
        this.data_nascimento = data_nascimento;
    }

    public String getEstado_civil() {
        return estado_civil;
    }

    public void setEstado_civil(String estado_civil) {
        this.estado_civil = estado_civil;
    }

    public boolean getTem_filhos() {
        return tem_filhos;
    }

    public void setTem_filhos(boolean tem_filhos) {
        this.tem_filhos = tem_filhos;
    }

    public String getEscolaridade() {
        return escolaridade;
    }

    public void setEscolaridade(String escolaridade) {
        this.escolaridade = escolaridade;
    }

    public String getProfissao() {
        return profissao;
    }

    public void setProfissao(String profissao) {
        this.profissao = profissao;
    }

    public String getEndereco() {
        return endereco;
    }

    public void setEndereco(String endereco) {
        this.endereco = endereco;
    }

    public String getNumero() {
        return numero;
    }

    public void setNumero(String numero) {
        this.numero = numero;
    }

    public String getComplemento() {
        return complemento;
    }

    public void setComplemento(String complemento) {
        this.complemento = complemento;
    }

    public String getBairro() {
        return bairro;
    }

    public void setBairro(String bairro) {
        this.bairro = bairro;
    }

    public String getCidade() {
        return cidade;
    }

    public void setCidade(String cidade) {
        this.cidade = cidade;
    }

    public String getUf() {
        return uf;
    }

    public void setUf(String uf) {
        this.uf = uf;
    }

    public String getCep() {
        return cep;
    }

    public void setCep(String cep) {
        this.cep = cep;
    }

    public String getTelefone() {
        return telefone;
    }

    public void setTelefone(String telefone) {
        this.telefone = telefone;
    }

    public String getCelular_1() {
        return celular_1;
    }

    public void setCelular_1(String celular_1) {
        this.celular_1 = celular_1;
    }

    public String getCelular_2() {
        return celular_2;
    }

    public void setCelular_2(String celular_2) {
        this.celular_2 = celular_2;
    }

    public List<T> getDoacoes() {
        return doacoes;
    }

    public void setDoacoes(List<T> doacoes) {
        this.doacoes = doacoes;
    }

    public List<T> getNecessidades_especiais() {
        return necessidades_especiais;
    }

    public void setNecessidades_especiais(List<T> necessidades_especiais) {
        this.necessidades_especiais = necessidades_especiais;
    }

    public String getSite() {
        return site;
    }

    public void setSite(String site) {
        this.site = site;
    }

    public String getFacebook() {
        return facebook;
    }

    public void setFacebook(String facebook) {
        this.facebook = facebook;
    }

    public String getInstagram() {
        return instagram;
    }

    public void setInstagram(String instagram) {
        this.instagram = instagram;
    }

    public String getYoutube() {
        return youtube;
    }

    public void setYoutube(String youtube) {
        this.youtube = youtube;
    }

    public String getVimeo() {
        return vimeo;
    }

    public void setVimeo(String vimeo) {
        this.vimeo = vimeo;
    }
}
