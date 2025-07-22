<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ContactServiceInterface;
use App\Http\Requests\ContactRequest;
use Illuminate\Http\Request;

class ContactController extends Controller
{

    public function __construct(protected ContactServiceInterface $contactService)
    {
    }

     public function index()
    {
        $contacts = $this->contactService->getPaginatedContacts(15);
        
        $stats = $this->contactService->getContactStats();
        
        return view('admin.contact.index', compact('contacts', 'stats'));
    }
    public function create()
    {
        return view('admin.contact.create');
    }

    public function store(ContactRequest $request)
    {
        try {
            $this->contactService->createContact($request->validated());
            return redirect()->route('admin.contact.index')
                ->with('success', 'Kontak berhasil dibuat.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(int $id)
    {
        $contact = $this->contactService->findContact($id);
        if (!$contact) {
            return redirect()->route('admin.contact.index')
                ->with('error', 'Kontak tidak ditemukan.');
        }
        return view('admin.contact.show', compact('contact'));
    }

    public function edit(int $id)
    {
        $contact = $this->contactService->findContact($id);
        if (!$contact) {
            return redirect()->route('admin.contact.index')
                ->with('error', 'Kontak tidak ditemukan.');
        }
        return view('admin.contact.edit', compact('contact'));
    }

    public function update(ContactRequest $request, int $id)
    {
        try {
            $contact = $this->contactService->updateContact($id, $request->validated());
            if (!$contact) {
                return redirect()->route('admin.contact.index')
                    ->with('error', 'Kontak tidak ditemukan.');
            }
            return redirect()->route('admin.contact.index')
                ->with('success', 'Kontak berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(int $id)
    {
        try {
            $deleted = $this->contactService->deleteContact($id);
            if (!$deleted) {
                return redirect()->route('admin.contact.index')
                    ->with('error', 'Kontak tidak ditemukan.');
            }
            return redirect()->route('admin.contact.index')
                ->with('success', 'Kontak berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // public function pending()
    // {
    //     $pendingContacts = $this->contactService->getPendingContacts();
    //     return view('admin.contact.pending', compact('pendingContacts'));
    // }

    // public function reply(int $id)
    // {
    //     $contact = $this->contactService->findContact($id);
    //     if (!$contact) {
    //         return redirect()->route('admin.contact.index')
    //             ->with('error', 'Kontak tidak ditemukan.');
    //     }
    //     return view('admin.contact.reply', compact('contact'));
    // }

    // public function storeReply(Request $request, int $id)
    // {
    //     $request->validate([
    //         'admin_reply' => 'required|string'
    //     ]);

    //     try {
    //         $replied = $this->contactService->replyToContact($id, $request->admin_reply);
    //         if (!$replied) {
    //             return redirect()->route('admin.contact.index')
    //                 ->with('error', 'Kontak tidak ditemukan.');
    //         }
    //         return redirect()->route('admin.contact.index')
    //             ->with('success', 'Balasan berhasil dikirim.');
    //     } catch (\Exception $e) {
    //         return redirect()->back()
    //             ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
    //             ->withInput();
    //     }
    // }

    // public function filterByStatus(Request $request)
    // {
    //     $request->validate([
    //         'status' => 'required|in:pending,replied'
    //     ]);

    //     $contacts = $this->contactService->getContactsByStatus($request->status);
    //     $status = $request->status;
        
    //     return view('admin.contact.filter', compact('contacts', 'status'));
    // }
}
