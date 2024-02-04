package com.nibblelab.smartchurch.model;

import com.google.gson.annotations.SerializedName;
import com.nibblelab.smartchurch.common.DateHelper;
import com.nibblelab.smartchurch.common.StringHelper;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import java.util.Calendar;
import java.util.Date;
import java.util.List;

public class AgendaData {
    @SerializedName("id")
    private String id;
    @SerializedName("tags")
    private List<String> tags;
    @SerializedName("nome")
    private String nome;
    @SerializedName("logo")
    private String logo;
    @SerializedName("ref")
    private String ref;
    @SerializedName("ref_tp")
    private String ref_tp;
    @SerializedName("responsavel")
    private String responsavel;
    @SerializedName("time_ini")
    private String time_ini;
    @SerializedName("time_end")
    private String time_end;
    @SerializedName("observacoes")
    private String observacoes;
    @SerializedName("recorrente")
    private boolean recorrente;
    @SerializedName("dias_horarios")
    private String dias_horarios;
    @SerializedName("endereco")
    private String endereco;
    @SerializedName("numero")
    private String numero;
    @SerializedName("complemento")
    private String complemento;
    @SerializedName("bairro")
    private String bairro;
    @SerializedName("cidade")
    private String cidade;
    @SerializedName("uf")
    private String uf;
    @SerializedName("cep")
    private String cep;
    @SerializedName("site")
    private String site;
    @SerializedName("facebook")
    private String facebook;
    @SerializedName("instagram")
    private String instagram;
    @SerializedName("youtube")
    private String youtube;
    @SerializedName("vimeo")
    private String vimeo;
    @SerializedName("stat")
    private String stat;
    @SerializedName("time_cad")
    private String time_cad;
    @SerializedName("last_mod")
    private String last_mod;
    @SerializedName("last_amod")
    private String last_amod;

    public AgendaData() {
    }

    public AgendaData(String id, List<String> tags, String nome, String logo, String ref, String ref_tp,
                      String responsavel, String time_ini, String time_end, String observacoes,
                      boolean recorrente, String dias_horarios, String endereco, String numero,
                      String complemento, String bairro, String cidade, String uf, String cep,
                      String site, String facebook, String instagram, String youtube, String vimeo,
                      String stat, String time_cad, String last_mod, String last_amod) {
        this.id = id;
        this.tags = tags;
        this.nome = nome;
        this.logo = logo;
        this.ref = ref;
        this.ref_tp = ref_tp;
        this.responsavel = responsavel;
        this.time_ini = time_ini;
        this.time_end = time_end;
        this.observacoes = observacoes;
        this.recorrente = recorrente;
        this.dias_horarios = dias_horarios;
        this.endereco = endereco;
        this.numero = numero;
        this.complemento = complemento;
        this.bairro = bairro;
        this.cidade = cidade;
        this.uf = uf;
        this.cep = cep;
        this.site = site;
        this.facebook = facebook;
        this.instagram = instagram;
        this.youtube = youtube;
        this.vimeo = vimeo;
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

    public List<String> getTags() {
        return tags;
    }

    public void setTags(List<String> tags) {
        this.tags = tags;
    }

    public String getNome() {
        return nome;
    }

    public void setNome(String nome) {
        this.nome = nome;
    }

    public String getLogo() {
        return logo;
    }

    public void setLogo(String logo) {
        this.logo = logo;
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

    public String getResponsavel() {
        return responsavel;
    }

    public void setResponsavel(String responsavel) {
        this.responsavel = responsavel;
    }

    public String getTime_ini() {
        return time_ini;
    }

    public void setTime_ini(String time_ini) {
        this.time_ini = time_ini;
    }

    public String getTime_end() {
        return time_end;
    }

    public void setTime_end(String time_end) {
        this.time_end = time_end;
    }

    public String getObservacoes() {
        return observacoes;
    }

    public void setObservacoes(String observacoes) {
        this.observacoes = observacoes;
    }

    public boolean isRecorrente() {
        return recorrente;
    }

    public void setRecorrente(boolean recorrente) {
        this.recorrente = recorrente;
    }

    public String getDias_horarios() {
        return dias_horarios;
    }

    public void setDias_horarios(String dias_horarios) {
        this.dias_horarios = dias_horarios;
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

    public Calendar getTimeIniCalendar()
    {
        if(StringHelper.notEmpty(this.time_ini)) {
            Date dt = DateHelper.fromDBTimeToDate(this.time_ini);
            return DateHelper.date2Calendar(dt);
        }
        return null;
    }

    public Calendar getTimeEndCalendar()
    {
        if(StringHelper.notEmpty(this.time_end)) {
            Date dt = DateHelper.fromDBTimeToDate(this.time_end);
            return DateHelper.date2Calendar(dt);
        }
        return null;
    }

    public boolean hasEnderecoData()
    {
        return (StringHelper.notEmpty(this.endereco) || StringHelper.notEmpty(this.numero) ||
                    StringHelper.notEmpty(this.bairro) || StringHelper.notEmpty(this.cidade) ||
                (StringHelper.notEmpty(this.uf) && !this.uf.equals("--")) || StringHelper.notEmpty(this.cep));
    }

    public String getEnderecoUrl()
    {
        String url = "";
        if(StringHelper.notEmpty(this.endereco)) {
            url += this.endereco.replace(" ", "+");
            if(StringHelper.notEmpty(this.numero)) {
                url += "%2C+" + this.numero;
            }
        }

        if(StringHelper.notEmpty(this.cidade)) {
            if(url.length() > 0) {
                url += "%2C+";
            }

            url += this.cidade.replace(" ", "+");
        }

        if(StringHelper.notEmpty(this.uf)) {
            if(url.length() > 0) {
                url += "%2C+";
            }

            url += this.uf;
        }

        return url;
    }

    public String getInstagramUrl()
    {
        String url = "";
        if(this.instagram.contains("@")) {
            url = "https://www.instagram.com/" + this.instagram.replace("@", "");
        }
        else {
            url = this.instagram;
        }

        return url;
    }

    public boolean checkIfDateHasRecorrente(String date)
    {
        int i, len;
        String dia_nome;
        String[] dias = {"domingo","segunda","terça","quarta","quinta","sexta","sábado"};

        try {
            JSONArray dias_horarios = new JSONArray(this.dias_horarios);
            len = dias_horarios.length();
            for(i = 0; i < len; i++)
            {
                JSONObject d = dias_horarios.getJSONObject(i);
                dia_nome = DateHelper.getDayNameFromHumanDate(date);
                if(StringHelper.notEmpty(dia_nome))
                {
                    for(String d_ : dias)
                    {
                        if(d.getBoolean(d_) && dia_nome.contains(d_))
                        {
                            return true;
                        }
                    }
                }
            }

        } catch (JSONException e) {

        }

        return false;
    }

    public String getTimeFromRecorrenteInDate(String date)
    {
        int i, len;
        String dia_nome;
        String[] dias = {"domingo","segunda","terça","quarta","quinta","sexta","sábado"};
        String horario = "";

        try {
            JSONArray dias_horarios = new JSONArray(this.dias_horarios);
            len = dias_horarios.length();
            for(i = 0; i < len; i++)
            {
                JSONObject dia = dias_horarios.getJSONObject(i);
                dia_nome = DateHelper.getDayNameFromHumanDate(date);
                if(StringHelper.notEmpty(dia_nome))
                {
                    for(String d_ : dias)
                    {
                        if(dia.getBoolean(d_) && dia_nome.contains(d_))
                        {
                            horario = dia.getString("inicio") + " - " + dia.getString("termino");
                        }
                    }
                }
            }

        } catch (JSONException e) {

        }

        return horario;
    }

}
