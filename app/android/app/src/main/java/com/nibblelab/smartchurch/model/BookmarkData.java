package com.nibblelab.smartchurch.model;

import com.google.gson.annotations.SerializedName;

public class BookmarkData {
    @SerializedName("id")
    private String id;
    @SerializedName("pessoa")
    private String pessoa;
    @SerializedName("ref")
    private String ref;
    @SerializedName("ref_tp")
    private String ref_tp;

    public BookmarkData() {
    }

    public BookmarkData(String id, String pessoa, String ref, String ref_tp) {
        this.id = id;
        this.pessoa = pessoa;
        this.ref = ref;
        this.ref_tp = ref_tp;
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
}
