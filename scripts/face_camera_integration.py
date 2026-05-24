"""
Face Recognition Camera Integration System
Avtomatik yuzni tanish va davomat tizimi
"""

import cv2
import numpy as np
import face_recognition
import requests
import json
import base64
import time
from datetime import datetime
from threading import Thread, Lock
import queue
import logging

# Setup logging
logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

class FaceCameraIntegration:
    """
    Kamera orqali real-time yuzni tanish tizimi
    """

    def __init__(self, api_url="http://localhost/api/face/stream", camera_id="entrance_main"):
        self.api_url = api_url
        self.camera_id = camera_id
        self.frame_queue = queue.Queue(maxsize=30)
        self.detection_lock = Lock()
        self.is_running = False

        # Cache for detected faces (5 minute cooldown)
        self.detection_cache = {}
        self.cache_timeout = 300  # 5 minutes

        # Performance settings
        self.frame_skip = 2  # Process every 2nd frame
        self.resize_scale = 0.25  # Resize for faster processing
        self.detection_threshold = 0.6

        # Statistics
        self.stats = {
            'frames_processed': 0,
            'faces_detected': 0,
            'faces_recognized': 0,
            'api_calls': 0
        }

    def start_camera(self, camera_source=0):
        """
        Kamerani ishga tushirish
        camera_source: 0 = default camera, or IP camera URL
        """
        self.cap = cv2.VideoCapture(camera_source)

        if not self.cap.isOpened():
            raise Exception(f"Cannot open camera {camera_source}")

        # Set camera properties for better performance
        self.cap.set(cv2.CAP_PROP_FRAME_WIDTH, 1280)
        self.cap.set(cv2.CAP_PROP_FRAME_HEIGHT, 720)
        self.cap.set(cv2.CAP_PROP_FPS, 30)

        self.is_running = True
        logger.info(f"Camera started: {camera_source}")

    def stop_camera(self):
        """Kamerani to'xtatish"""
        self.is_running = False
        if hasattr(self, 'cap'):
            self.cap.release()
        cv2.destroyAllWindows()
        logger.info("Camera stopped")

    def process_frame(self, frame):
        """
        Framedan yuzlarni aniqlash va tanish
        """
        # Resize for faster processing
        small_frame = cv2.resize(frame, (0, 0), fx=self.resize_scale, fy=self.resize_scale)
        rgb_frame = cv2.cvtColor(small_frame, cv2.COLOR_BGR2RGB)

        # Find face locations and encodings
        face_locations = face_recognition.face_locations(rgb_frame, model="hog")

        if not face_locations:
            return frame, []

        # Scale back up face locations
        face_locations = [(int(top/self.resize_scale), int(right/self.resize_scale),
                          int(bottom/self.resize_scale), int(left/self.resize_scale))
                          for (top, right, bottom, left) in face_locations]

        detected_faces = []

        for (top, right, bottom, left) in face_locations:
            # Extract face image
            face_image = frame[top:bottom, left:right]

            # Check cache to avoid duplicate detections
            face_key = f"{left}_{top}"
            current_time = time.time()

            if face_key in self.detection_cache:
                if current_time - self.detection_cache[face_key] < self.cache_timeout:
                    continue

            # Convert to base64
            _, buffer = cv2.imencode('.jpg', face_image)
            face_base64 = base64.b64encode(buffer).decode('utf-8')

            detected_faces.append({
                'image': face_base64,
                'location': {'top': top, 'right': right, 'bottom': bottom, 'left': left}
            })

            # Update cache
            self.detection_cache[face_key] = current_time

            # Draw rectangle around face
            cv2.rectangle(frame, (left, top), (right, bottom), (0, 255, 0), 2)

        self.stats['faces_detected'] += len(detected_faces)

        return frame, detected_faces

    def send_to_api(self, frame_data):
        """
        API ga frame ma'lumotlarini yuborish
        """
        try:
            _, buffer = cv2.imencode('.jpg', frame_data['frame'])
            frame_base64 = base64.b64encode(buffer).decode('utf-8')

            payload = {
                'frame': frame_base64,
                'camera_id': self.camera_id,
                'timestamp': int(time.time())
            }

            response = requests.post(
                f"{self.api_url}/process",
                json=payload,
                headers={'Content-Type': 'application/json'},
                timeout=5
            )

            self.stats['api_calls'] += 1

            if response.status_code == 200:
                result = response.json()
                if result.get('processed_users'):
                    self.stats['faces_recognized'] += len(result['processed_users'])
                    self.handle_recognition_result(result['processed_users'])

            return response.json()

        except Exception as e:
            logger.error(f"API error: {e}")
            return None

    def handle_recognition_result(self, users):
        """
        Tanilgan foydalanuvchilar bilan ishlash
        """
        for user in users:
            action = "KIRDI" if user['action'] == 'check_in' else "CHIQDI"
            logger.info(f"[{user['time']}] {user['name']} - {action} (Aniqlik: {user['confidence']}%)")

            # Display notification on screen
            self.show_notification(user)

    def show_notification(self, user):
        """
        Ekranda bildirishnoma ko'rsatish
        """
        # This would be implemented based on your UI requirements
        print(f"\n{'='*50}")
        print(f"YUZNI TANISH TIZIMI")
        print(f"{'='*50}")
        print(f"Foydalanuvchi: {user['name']}")
        print(f"Harakat: {'KIRISH' if user['action'] == 'check_in' else 'CHIQISH'}")
        print(f"Vaqt: {user['time']}")
        print(f"Aniqlik: {user['confidence']}%")
        print(f"{'='*50}\n")

    def frame_processor_thread(self):
        """
        Background thread for processing frames
        """
        frame_counter = 0

        while self.is_running:
            if not self.frame_queue.empty():
                frame = self.frame_queue.get()
                frame_counter += 1

                # Skip frames for performance
                if frame_counter % self.frame_skip == 0:
                    processed_frame, faces = self.process_frame(frame)

                    if faces:
                        # Send to API in background
                        Thread(target=self.send_to_api, args=({'frame': frame},)).start()

                    self.stats['frames_processed'] += 1

            time.sleep(0.01)  # Small delay to prevent CPU overload

    def run(self, display=True):
        """
        Asosiy ishga tushirish funktsiyasi
        """
        try:
            # Start frame processor thread
            processor_thread = Thread(target=self.frame_processor_thread)
            processor_thread.start()

            logger.info("Face recognition system started")

            while self.is_running:
                ret, frame = self.cap.read()

                if not ret:
                    logger.error("Failed to read frame")
                    break

                # Add frame to queue for processing
                if not self.frame_queue.full():
                    self.frame_queue.put(frame.copy())

                if display:
                    # Process frame for display
                    display_frame, _ = self.process_frame(frame)

                    # Add status overlay
                    self.add_status_overlay(display_frame)

                    # Show frame
                    cv2.imshow('Yuzni Tanish Tizimi - Monitoring', display_frame)

                    # Check for exit (ESC key)
                    if cv2.waitKey(1) & 0xFF == 27:
                        break

            processor_thread.join()

        except Exception as e:
            logger.error(f"Runtime error: {e}")

        finally:
            self.stop_camera()
            self.print_statistics()

    def add_status_overlay(self, frame):
        """
        Framega status ma'lumotlarini qo'shish
        """
        height, width = frame.shape[:2]

        # Add semi-transparent overlay
        overlay = frame.copy()
        cv2.rectangle(overlay, (0, 0), (width, 60), (0, 0, 0), -1)
        frame = cv2.addWeighted(frame, 0.7, overlay, 0.3, 0)

        # Add text
        font = cv2.FONT_HERSHEY_SIMPLEX
        cv2.putText(frame, f"YUZNI TANISH TIZIMI - {self.camera_id}",
                   (10, 25), font, 0.7, (255, 255, 255), 2)
        cv2.putText(frame, f"Aniqlangan: {self.stats['faces_detected']} | Tanilgan: {self.stats['faces_recognized']}",
                   (10, 50), font, 0.5, (0, 255, 0), 1)

        # Add timestamp
        timestamp = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
        cv2.putText(frame, timestamp, (width - 200, 25), font, 0.5, (255, 255, 255), 1)

        return frame

    def print_statistics(self):
        """
        Statistikalarni chiqarish
        """
        print("\n" + "="*50)
        print("SESSIYA STATISTIKASI")
        print("="*50)
        print(f"Qayta ishlangan framelar: {self.stats['frames_processed']}")
        print(f"Aniqlangan yuzlar: {self.stats['faces_detected']}")
        print(f"Tanilgan foydalanuvchilar: {self.stats['faces_recognized']}")
        print(f"API so'rovlar: {self.stats['api_calls']}")
        print("="*50 + "\n")


class MultiCameraManager:
    """
    Ko'p kameralarni boshqarish
    """

    def __init__(self):
        self.cameras = {}

    def add_camera(self, camera_id, camera_source, api_url):
        """
        Yangi kamera qo'shish
        """
        camera = FaceCameraIntegration(api_url=api_url, camera_id=camera_id)
        camera.start_camera(camera_source)

        # Run in separate thread
        camera_thread = Thread(target=camera.run, args=(False,))
        camera_thread.start()

        self.cameras[camera_id] = {
            'instance': camera,
            'thread': camera_thread
        }

        logger.info(f"Camera added: {camera_id}")

    def remove_camera(self, camera_id):
        """
        Kamerani o'chirish
        """
        if camera_id in self.cameras:
            self.cameras[camera_id]['instance'].stop_camera()
            self.cameras[camera_id]['thread'].join()
            del self.cameras[camera_id]
            logger.info(f"Camera removed: {camera_id}")

    def get_all_stats(self):
        """
        Barcha kameralar statistikasi
        """
        stats = {}
        for camera_id, camera_data in self.cameras.items():
            stats[camera_id] = camera_data['instance'].stats
        return stats


# CLI interface
def main():
    """
    Asosiy ishga tushirish
    """
    print("\n" + "="*60)
    print("YUZNI TANISH VA DAVOMAT TIZIMI")
    print("="*60)
    print("\nTanlang:")
    print("1. Bitta kamera")
    print("2. Ko'p kameralar")
    print("3. Test rejimi")

    choice = input("\nTanlang (1-3): ")

    if choice == "1":
        # Single camera mode
        api_url = input("API URL (default: http://localhost/api/face/stream): ") or "http://localhost/api/face/stream"
        camera_source = input("Kamera manbasi (0 = default, yoki IP): ") or 0

        if camera_source != 0:
            camera_source = str(camera_source)
        else:
            camera_source = int(camera_source)

        system = FaceCameraIntegration(api_url=api_url)
        system.start_camera(camera_source)
        system.run(display=True)

    elif choice == "2":
        # Multi-camera mode
        manager = MultiCameraManager()

        while True:
            print("\n1. Kamera qo'shish")
            print("2. Kamera o'chirish")
            print("3. Statistika")
            print("4. Chiqish")

            action = input("\nTanlang: ")

            if action == "1":
                camera_id = input("Kamera ID: ")
                camera_source = input("Kamera manbasi: ")
                api_url = input("API URL: ")
                manager.add_camera(camera_id, camera_source, api_url)

            elif action == "2":
                camera_id = input("O'chiriladigan kamera ID: ")
                manager.remove_camera(camera_id)

            elif action == "3":
                stats = manager.get_all_stats()
                print("\nSTATISTIKA:")
                for cam_id, cam_stats in stats.items():
                    print(f"\n{cam_id}: {cam_stats}")

            elif action == "4":
                for camera_id in list(manager.cameras.keys()):
                    manager.remove_camera(camera_id)
                break

    elif choice == "3":
        # Test mode
        print("\nTest rejimi ishga tushirilmoqda...")
        system = FaceCameraIntegration(api_url="http://localhost/api/face/stream")
        system.start_camera(0)
        system.run(display=True)


if __name__ == "__main__":
    try:
        main()
    except KeyboardInterrupt:
        print("\n\nTizim to'xtatildi.")
    except Exception as e:
        logger.error(f"Xatolik: {e}")
        print(f"\nXatolik yuz berdi: {e}")