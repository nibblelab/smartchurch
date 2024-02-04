package com.nibblelab.smartchurch.model;

import com.google.gson.annotations.SerializedName;
import com.nibblelab.smartchurch.common.StringHelper;

import java.util.List;

public class UserData {

    @SerializedName("id")
    private String id;
    @SerializedName("nome")
    private String nome;
    @SerializedName("email")
    private String email;
    @SerializedName("avatar")
    private String avatar;
    @SerializedName("perms")
    private List<PermsData> perms;
    @SerializedName("membresia")
    private MembresiaData membresia;
    @SerializedName("modulos")
    private String modulos;
    @SerializedName("time_cad")
    private String time_cad;
    @SerializedName("last_mod")
    private String last_mod;
    @SerializedName("last_sync")
    private String last_sync;

    public UserData() {
    }

    public UserData(String id, String nome, String email, String avatar, List<PermsData> perms, MembresiaData membresia,
                        String modulos, String time_cad, String last_mod, String last_sync) {
        this.id = id;
        this.nome = nome;
        this.email = email;
        this.avatar = avatar;
        this.perms = perms;
        this.membresia = membresia;
        this.modulos = modulos;
        this.time_cad = time_cad;
        this.last_mod = last_mod;
        this.last_sync = last_sync;
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

    public String getEmail() {
        return email;
    }

    public void setEmail(String email) {
        this.email = email;
    }

    public String getAvatar() {
        return avatar;
    }

    public void setAvatar(String avatar) {
        this.avatar = avatar;
    }

    public List<PermsData> getPerms() {
        return perms;
    }

    public void setPerms(List<PermsData> perms) {
        this.perms = perms;
    }

    public String getModulos() {
        return modulos;
    }

    public void setModulos(String modulos) {
        this.modulos = modulos;
    }

    public MembresiaData getMembresia() { return membresia; }

    public void setMembresia(MembresiaData membresia) { this.membresia = membresia; }

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

    public String getLast_sync() {
        return last_sync;
    }

    public void setLast_sync(String last_sync) {
        this.last_sync = last_sync;
    }

    public static boolean doIHavePermission(List<PermsData> perms, String perm)
    {
        for(PermsData p : perms)
        {
            if(p.getNome().equals(perm)) {
                return true;
            }
        }

        return false;
    }

    public static boolean doIHaveMod(String modulos, String mod)
    {
        if(!StringHelper.notEmpty(modulos)) {
            return false;
        }

        return modulos.contains(mod);
    }

}
