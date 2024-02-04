package com.nibblelab.smartchurch.fragments;

import android.content.Context;
import android.os.Bundle;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.RelativeLayout;

import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;

import com.google.android.material.floatingactionbutton.FloatingActionButton;
import com.nibblelab.smartchurch.API.MuralAPI;
import com.nibblelab.smartchurch.API.responses.ApiResponse;
import com.nibblelab.smartchurch.R;
import com.nibblelab.smartchurch.adapters.MuralAdapter;
import com.nibblelab.smartchurch.model.MuralData;
import com.nibblelab.smartchurch.ui.events.MuralListEvents;

import java.util.List;

public class BookmarkFragment extends BaseFragment implements MuralListEvents {

    private BookmarkFragment.OnBookmarkFragInteractionListener mListener;
    private static final String TAG = "BookmarkFragment";

    // mural
    RecyclerView muralView;
    RecyclerView.LayoutManager muralLayoutManager;
    MuralAdapter muralAdapter;

    // controles
    FloatingActionButton bookmarkListPrev;
    FloatingActionButton bookmarkListNext;
    String pessoa;
    int page = 1;
    int pageSize = 5;

    public BookmarkFragment() {
    }

    public static BookmarkFragment newInstance() {
        BookmarkFragment fragment = new BookmarkFragment();
        Bundle args = new Bundle();
        fragment.setArguments(args);
        return fragment;
    }

    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        if (getArguments() != null) {

        }
    }

    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container,
                             Bundle savedInstanceState) {
        // Inflate the layout for this fragment
        View inf = inflater.inflate(R.layout.fragment_bookmark, container, false);

        activity.setToolbarTitle(R.string.menu_like);

        pessoa = activity.getUser().getId();

        muralView = (RecyclerView) inf.findViewById(R.id.mural_list);

        // controles
        bookmarkListPrev = (FloatingActionButton) inf.findViewById(R.id.bookmark_anterior);
        bookmarkListNext = (FloatingActionButton) inf.findViewById(R.id.bookmark_proximo);

        bookmarkListPrev.hide();
        bookmarkListNext.hide();

        // handler do botão de próxima página na listagem
        bookmarkListPrev.setOnClickListener(v -> {
            toPrevPage();
        });

        // handler do botão de página anterior na listagem
        bookmarkListNext.setOnClickListener(v -> {
            toNextPage();
        });

        this.loadBookmarkedMural();

        return inf;
    }

    /**
     * Veja se tem páginas a serem exibidas
     *
     * @param max
     * @return
     */
    private boolean hasNextPage(int max)
    {
        int current = page * pageSize;
        return (current < max);
    }

    /**
     * Vá para a próxima página
     */
    public void toNextPage()
    {
        page++;
        this.loadBookmarkedMural();
    }

    /**
     * Volte à página anterior
     */
    public void toPrevPage()
    {
        page--;
        this.loadBookmarkedMural();
    }

    public void loadBookmarkedMural()
    {
        activity.showLoadingSpinner();
        MuralAPI api = new MuralAPI(activity);
        api.getMuraisBookmarkedDaPessoa(pessoa, page, pageSize, new ApiResponse<List<MuralData>>() {
            @Override
            public void onResponse() {
                activity.hideLoadingSpinner();
            }

            @Override
            public void onResponse(List<MuralData> data) {
                activity.hideLoadingSpinner();
            }

            @Override
            public void onResponse(List<MuralData> data, int total) {
                activity.hideLoadingSpinner();
                BookmarkFragment.this.renderBookmarkedMural(data, total);
            }

            @Override
            public void onAlreadyExecuted() { }

            @Override
            public void onError(String msg) {
                activity.hideLoadingSpinner();
                Log.d(TAG, "Erro: " + msg);
            }

            @Override
            public void onFail(Object fail) {
                activity.hideLoadingSpinner();
                Log.d(TAG, "falha");
            }
        });
    }

    public void renderBookmarkedMural(List<MuralData> data, int total)
    {
        if(total > 0) {
            muralLayoutManager = new LinearLayoutManager(this.getContext());
            muralView.setLayoutManager(muralLayoutManager);

            muralAdapter = new MuralAdapter(this.getContext(), data,this, getLifecycle(), activity);
            muralView.setAdapter(muralAdapter);

            // ajuste a altura
            RelativeLayout.LayoutParams lp = new RelativeLayout.LayoutParams(RelativeLayout.LayoutParams.MATCH_PARENT, RelativeLayout.LayoutParams.MATCH_PARENT);
            muralView.setLayoutParams(lp);

            muralView.setVisibility(View.VISIBLE);
        }
        else {
            activity.infoDialog(":(", "Sem dados para exibir");
        }

        // veja se é possível exibir o botão de página anterior
        if(page == 1) {
            bookmarkListPrev.hide();
        }
        else {
            bookmarkListPrev.show();
        }

        // veja se é possível exibir o botão de próxima página
        if(hasNextPage(total)) {
            bookmarkListNext.show();
        }
        else {
            bookmarkListNext.hide();
        }
    }

    @Override
    public void onAttach(Context context) {
        super.onAttach(context);
        if (context instanceof BookmarkFragment.OnBookmarkFragInteractionListener) {
            mListener = (BookmarkFragment.OnBookmarkFragInteractionListener) context;
        } else {
            throw new RuntimeException(context.toString()
                    + " must implement OnBookmarkFragInteractionListener");
        }
    }

    @Override
    public void onDetach() {
        super.onDetach();
        mListener = null;
    }

    @Override
    public void onAddToBookmark(Object object) {

    }

    @Override
    public void onRemoveFromBookmark() {
        this.loadBookmarkedMural();
    }

    public interface OnBookmarkFragInteractionListener {
        void onBookmarkFragInteraction();
    }
}
