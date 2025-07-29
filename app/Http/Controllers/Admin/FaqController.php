<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\FaqServiceInterface;
use App\Http\Requests\FaqRequest;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    public function __construct(protected FaqServiceInterface $faqService)
    {
        
    }

    public function index()
    {
        $faqs = $this->faqService->getPaginatedFaqs(15);
        return view('admin.faq.index', compact('faqs'));
    }

    public function create()
    {
        return view('admin.faq.create');
    }

    public function store(FaqRequest $request)
    {
        try {
            $this->faqService->createFaq($request->validated());
            return redirect()->route('admin.faq.index')
                ->with('success', 'FAQ berhasil dibuat.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show($locale, int $id)
    {
        $faq = $this->faqService->findFaq($id);
        if (!$faq) {
            return redirect()->route('admin.faq.index')
                ->with('error', 'FAQ tidak ditemukan.');
        }
        return view('admin.faq.show', compact('faq'));
    }

    public function edit($locale, int $id)
    {
        $faq = $this->faqService->findFaq($id);
        if (!$faq) {
            return redirect()->route('admin.faq.index')
                ->with('error', 'FAQ tidak ditemukan.');
        }
        return view('admin.faq.edit', compact('faq'));
    }

    public function update(FaqRequest $request, $locale, int $id)
    {
        try {
            $faq = $this->faqService->updateFaq($id, $request->validated());
            if (!$faq) {
                return redirect()->route('admin.faq.index')
                    ->with('error', 'FAQ tidak ditemukan.');
            }
            return redirect()->route('admin.faq.index')
                ->with('success', 'FAQ berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy($locale, int $id)
    {
        try {
            $deleted = $this->faqService->deleteFaq($id);
            if (!$deleted) {
                return redirect()->route('admin.faq.index')
                    ->with('error', 'FAQ tidak ditemukan.');
            }
            return redirect()->route('admin.faq.index')
                ->with('success', 'FAQ berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function toggleStatus($locale, int $id)
    {
        try {
            $toggled = $this->faqService->toggleFaqStatus($id);
            if (!$toggled) {
                return response()->json(['error' => 'FAQ tidak ditemukan.'], 404);
            }
            return response()->json(['success' => 'Status FAQ berhasil diubah.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    public function reorder(Request $request)
    {
        try {
            $request->validate([
                'order' => 'required|array',
                'order.*.id' => 'required|integer',
                'order.*.display_order' => 'required|integer',
            ]);

            $reordered = $this->faqService->reorderFaqs($request->input('order'));
            if (!$reordered) {
                return response()->json(['error' => 'Gagal mengurutkan FAQ.'], 500);
            }
            return response()->json(['success' => 'FAQ berhasil diurutkan.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }
}