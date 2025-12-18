from PIL import Image, ImageFilter
import random

# Create a small texture image (this will be tiled)
size = 128
img = Image.new('RGB', (size, size), color=(254, 243, 199))  # #fef3c7

# Add noise
pixels = img.load()
for i in range(size):
    for j in range(size):
        # Add subtle random noise
        noise = random.randint(-15, 15)
        r, g, b = img.getpixel((i, j))
        r = max(0, min(255, r + noise))
        g = max(0, min(255, g + noise))
        b = max(0, min(255, b + noise))
        pixels[i, j] = (r, g, b)

# Apply slight blur for smoothness
img = img.filter(ImageFilter.GaussianBlur(radius=0.5))

# Save the texture
img.save('public/paper-texture.png')
print("✓ Paper texture created at public/paper-texture.png")
