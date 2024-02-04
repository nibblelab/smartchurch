package com.nibblelab.smartchurch.adapters;

import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.webkit.WebView;
import android.widget.Button;
import android.widget.LinearLayout;
import android.widget.TextView;

import androidx.annotation.NonNull;
import androidx.recyclerview.widget.RecyclerView;

import com.nibblelab.smartchurch.R;
import com.nibblelab.smartchurch.activity.Base;
import com.nibblelab.smartchurch.common.StringHelper;
import com.nibblelab.smartchurch.model.PedidoDeOracaoData;
import com.nibblelab.smartchurch.ui.events.PedidoOracaoListEvents;

import org.jetbrains.annotations.NotNull;

import java.util.List;

public class PedidoDeOracaoAdapter extends RecyclerView.Adapter<PedidoDeOracaoAdapter.RecyclerPedidoDeOracaoViewHolder> {
    Context ctx;
    List<PedidoDeOracaoData> list;
    public static PedidoOracaoListEvents events;
    String userId;

    public PedidoDeOracaoAdapter(Context ctx, String userId, List<PedidoDeOracaoData> data, PedidoOracaoListEvents evt)
    {
        this.ctx = ctx;
        this.list = data;
        this.events = evt;
        this.userId = userId;
    }

    @NonNull
    @NotNull
    @Override
    public RecyclerPedidoDeOracaoViewHolder onCreateViewHolder(@NonNull @NotNull ViewGroup viewGroup, int viewType) {
        View itemView = LayoutInflater.from(viewGroup.getContext()).inflate(R.layout.oracao_it_layout, viewGroup, false);
        return new PedidoDeOracaoAdapter.RecyclerPedidoDeOracaoViewHolder(itemView);
    }

    @Override
    public void onBindViewHolder(@NonNull @NotNull PedidoDeOracaoAdapter.RecyclerPedidoDeOracaoViewHolder viewHolder, int i) {
        PedidoDeOracaoData c = list.get(i);

        if(StringHelper.notEmpty(c.getAutorNome()))
        {
            viewHolder.autor.setText(c.getAutorNome());
        }

        if(StringHelper.notEmpty(c.getPedido()))
        {
            viewHolder.pedido.loadDataWithBaseURL(null, c.getPedido(), "text/html", "utf-8", null);
        }

        // opções de edição e remoção se o autor do pedido é o usuário
        if(this.userId.equals(c.getAutor())) {
            viewHolder.opts.setVisibility(View.VISIBLE);
        }

    }


    @Override
    public int getItemCount() {
        return list.size();
    }

    protected class RecyclerPedidoDeOracaoViewHolder extends RecyclerView.ViewHolder
    {
        protected TextView autor;
        protected WebView pedido;
        protected Button edit;
        protected Button delete;
        protected LinearLayout opts;

        public RecyclerPedidoDeOracaoViewHolder(final View v)
        {
            super(v);

            autor = (TextView) v.findViewById(R.id.oracao_autor);
            pedido = (WebView) v.findViewById(R.id.oracao_pedido);
            edit = (Button) v.findViewById(R.id.edit_pedido);
            delete = (Button) v.findViewById(R.id.delete_pedido);
            opts = (LinearLayout) v.findViewById(R.id.opts_pedido);

            edit.setOnClickListener(view -> events.onEditPedidoOracao(list.get(getLayoutPosition())));
            delete.setOnClickListener(view -> events.onDeletePedidoOracao(list.get(getLayoutPosition())));
        }
    }
}
