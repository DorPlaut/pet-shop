document.addEventListener('DOMContentLoaded', function () {
  // Get canvas elements - check each one separately
  const heroCanvas = document.getElementById('hero-canvas');
  const footerCanvas = document.getElementById('footer-canvas');

  // Enhanced configuration
  const config = {
    baseSpeed: 0.005, // Base animation speed
    autoAnimateStrength: 0.1, // Strength of automatic animation (0-1)
  };

  // Function to initialize WebGL for a given canvas
  function createRenderer(canvasElement) {
    if (!canvasElement) return null;

    return new THREE.WebGLRenderer({
      canvas: canvasElement,
      antialias: true,
      alpha: true,
    });
  }

  // Create shader material factory function
  function createMaterial() {
    return new THREE.ShaderMaterial({
      uniforms: {
        u_time: { value: 0.0 },
        u_resolution: { value: new THREE.Vector2(1, 1) },
        u_mouse: { value: new THREE.Vector2(0, 0) },
        u_autoAnimateStrength: { value: config.autoAnimateStrength },
      },
      vertexShader: `
        precision lowp float;
        precision lowp int;

        varying vec2 vUv;
        varying vec3 vertexPosition;

        void main() {
            vUv = uv;
            vertexPosition = position;      
            gl_Position = vec4(position, 1.0);
        }
      `,
      fragmentShader: `
        precision highp float;
        precision highp int;

        uniform vec2 u_resolution;
        uniform float u_time;
        uniform vec2 u_mouse;  // Mouse position in pixels
        uniform float u_autoAnimateStrength; 

        // Better hash function
        vec2 hash2(vec2 p) {
            p = vec2(dot(p, vec2(127.1, 311.7)), dot(p, vec2(269.5, 183.3)));
            return fract(sin(p) * 43758.5453123);
        }

        // Improved noise function
        float noise(vec2 p) {
            vec2 i = floor(p);
            vec2 f = fract(p);
            
            // Cubic Hermite curve
            vec2 u = f * f * (3.0 - 2.0 * f);
            
            // Mix 4 corners
            float a = mix(
                mix(dot(hash2(i + vec2(0.0, 0.0)) * 2.0 - 1.0, f - vec2(0.0, 0.0)),
                    dot(hash2(i + vec2(1.0, 0.0)) * 2.0 - 1.0, f - vec2(1.0, 0.0)), u.x),
                mix(dot(hash2(i + vec2(0.0, 1.0)) * 2.0 - 1.0, f - vec2(0.0, 1.0)),
                    dot(hash2(i + vec2(1.0, 1.0)) * 2.0 - 1.0, f - vec2(1.0, 1.0)), u.x), u.y);
            
            return 0.5 + 0.5 * a;
        }

        // FBM function for distortion
        float fbm(vec2 p, int octaves) {
            float value = 0.0;
            float amplitude = 0.5;
            float frequency = 1.0;
            
            for (int i = 0; i < 6; i++) {
                if (i >= octaves) break;
                value += amplitude * noise(p * frequency);
                frequency *= 2.17;
                amplitude *= 0.5;
            }
            
            return value;
        }

        void main() {
            vec2 uv = gl_FragCoord.xy / u_resolution.xy;
            float aspect = u_resolution.x / u_resolution.y;
            uv.x *= aspect;

            // slow timer
            float time = u_time * 0.3;
            
            // Normalize mouse position to 0-1 range and adjust for aspect ratio
            vec2 mouse = u_mouse / u_resolution.xy;
            mouse.x *= aspect;
            
            vec3 backgroundColor = vec3(1.0);
            vec3 blobColor = vec3(1.0, 0.9098, 0.8196);
            
            vec3 color = backgroundColor;
            
            // Number of blobs to distribute using golden ratio spiral
            const int numBlobs = 15;
            
            for (int i = 0; i < numBlobs; i++) {
                float fi = float(i);
                
                // Golden angle in radians (~137.5 degrees)
                float goldenAngle = 2.39996323;
                float theta = fi * goldenAngle;
                
                // Normalized radius (0-1 range, with slight inward buffer)
                float normalizedIndex = fi / float(numBlobs);
                float radius = sqrt(normalizedIndex) * 0.85;
                
                // Position on screen (0-1 range, centered)
                vec2 blobPos = vec2(
                    0.5 + radius * cos(theta),
                    0.5 + radius * sin(theta)
                );
                
                // Adjust for aspect ratio
                blobPos.x *= aspect;
                
                // Add subtle jitter for more natural look
                vec2 jitter = hash2(vec2(fi * 12.345, 67.89)) * 0.02 - 0.01;
                blobPos += jitter;
                
                // ===== ENHANCED CONSTANT ANIMATION =====
                // Add autonomous circular motion
                float timeOffset = fi * 0.5;
                float orbitSpeed = 0.2 + hash2(vec2(fi * 0.33, 0.78)).x * 0.1;
                
                // Circular orbit motion
                vec2 orbitPos = vec2(
                    sin(time * orbitSpeed + timeOffset) * 0.02,
                    cos(time * orbitSpeed * 1.3 + timeOffset) * 0.02
                );
                
                // Breathing effect (expansion and contraction)
                float breathingFreq = 0.3 + hash2(vec2(fi * 0.5, 0.2)).x * 0.2;
                float breathing = sin(time * breathingFreq + timeOffset * 3.0) * 0.01;
                
                // Apply autonomous animation with strength control
                blobPos += orbitPos * u_autoAnimateStrength;
                
                // Calculate distance from blob to mouse
                float mouseDistance = length(blobPos - mouse);
                
                // Mouse interaction - blobs move away from mouse
                float mouseRepelStrength = 0.0005 / (mouseDistance + 0.1);
                mouseRepelStrength = min(mouseRepelStrength, 0.15); // Limit maximum effect
                
                // Direction away from mouse
                vec2 repelDir = normalize(blobPos - mouse);
                
                // Apply repel effect with falloff based on distance
                blobPos += repelDir * mouseRepelStrength;
                
                // Get distance to blob center
                vec2 toBlob = uv - blobPos;
                float baseDist = length(toBlob);
                
                // Create distortion field
                float noiseScale = 4.0 + hash2(vec2(fi * 78.912, 34.56)).x * 1.0;
                vec2 noisePos = toBlob * noiseScale + vec2(
                    sin(time * 0.05 + timeOffset), 
                    cos(time * 0.05 + timeOffset)
                );
                
                // Strong distortion for more amorphous shapes
                float distortion = fbm(noisePos, 2) * 0.5;
                
                // Add time-based pulsing to distortion
                distortion += sin(time * (0.2 + hash2(vec2(fi * 0.7, 0.3)).x * 0.1) + fi) * 0.05 * u_autoAnimateStrength;
                
                // Mouse affects blob distortion - closer to mouse = more distortion
                float mouseDistortionEffect = 0.2 / (mouseDistance + 0.2);
                mouseDistortionEffect = min(mouseDistortionEffect, 0.5); // Limit maximum effect
                distortion += mouseDistortionEffect * 0.2 * sin(time * 2.0 + fi);
                
                // Vary blob size based on position - smaller near center, larger toward edges
                float blobSize = mix(0.001, 0.001, normalizedIndex);
                // Add slight randomness to size
                blobSize += hash2(vec2(fi * 23.456, 78.90)).x * 0.08;
                
                // Add breathing effect to blob size
                blobSize += breathing * u_autoAnimateStrength;
                
                // Mouse affects blob size - closer to mouse = slightly larger
                float mouseSizeEffect = 0.01 / (mouseDistance + 0.5);
                mouseSizeEffect = min(mouseSizeEffect, 0.05); // Limit maximum effect
                blobSize += mouseSizeEffect * 0.1;
                
                // Create highly distorted blob
                float blob = smoothstep(
                    blobSize + distortion,
                    blobSize + distortion - 0.002,
                    baseDist
                );
                
                // Mix colors
                color = mix(color, blobColor, blob);
            }
            
            gl_FragColor = vec4(color, 1.0);
        }
      `,
      transparent: true,
    });
  }

  // Create scene and camera factory function
  function createScene() {
    const scene = new THREE.Scene();
    const camera = new THREE.OrthographicCamera(-1, 1, 1, -1, 0.1, 10);
    camera.position.z = 1;
    return { scene, camera };
  }

  // Function to handle canvas resizing
  function resizeCanvas(renderer, canvasElement, material) {
    if (!renderer || !canvasElement) return;

    const pixelRatio = Math.min(window.devicePixelRatio, 2);
    const width = canvasElement.clientWidth;
    const height = canvasElement.clientHeight;

    if (
      canvasElement.width !== width * pixelRatio ||
      canvasElement.height !== height * pixelRatio
    ) {
      console.log(`Resizing ${canvasElement.id} to:`, width, 'x', height);
      renderer.setSize(width, height, false);
      renderer.setPixelRatio(pixelRatio);
      material.uniforms.u_resolution.value.set(width, height);
    }
  }

  // Create plane geometry and attach the material
  function createMesh(scene, material) {
    if (!scene) return;
    const geometry = new THREE.PlaneGeometry(2, 2);
    const mesh = new THREE.Mesh(geometry, material);
    scene.add(mesh);
    return mesh;
  }

  // Track mouse position
  let mousePosition = new THREE.Vector2(0, 0);

  document.addEventListener('mousemove', (event) => {
    // Update mouse position for all canvases
    mousePosition.x = event.clientX;
    mousePosition.y = event.clientY;
  });

  // Initialize Hero Canvas (if it exists)
  let heroRenderer, heroScene, heroCamera, heroMaterial;
  if (heroCanvas) {
    heroRenderer = createRenderer(heroCanvas);
    const heroSceneObj = createScene();
    heroScene = heroSceneObj.scene;
    heroCamera = heroSceneObj.camera;
    heroMaterial = createMaterial();
    createMesh(heroScene, heroMaterial);
  }

  // Initialize Footer Canvas (if it exists)
  let footerRenderer, footerScene, footerCamera, footerMaterial;
  if (footerCanvas) {
    footerRenderer = createRenderer(footerCanvas);
    const footerSceneObj = createScene();
    footerScene = footerSceneObj.scene;
    footerCamera = footerSceneObj.camera;
    footerMaterial = createMaterial();
    createMesh(footerScene, footerMaterial);
  }

  // Animation loop
  function animate() {
    requestAnimationFrame(animate);

    const time = performance.now() * 0.001; // Use seconds for more intuitive timing

    // Update and render hero canvas if it exists
    if (heroRenderer && heroMaterial) {
      resizeCanvas(heroRenderer, heroCanvas, heroMaterial);
      heroMaterial.uniforms.u_time.value = time;
      heroMaterial.uniforms.u_mouse.value.copy(mousePosition);
      heroRenderer.render(heroScene, heroCamera);
    }

    // Update and render footer canvas if it exists
    if (footerRenderer && footerMaterial) {
      resizeCanvas(footerRenderer, footerCanvas, footerMaterial);
      footerMaterial.uniforms.u_time.value = time;
      footerMaterial.uniforms.u_mouse.value.copy(mousePosition);
      footerRenderer.render(footerScene, footerCamera);
    }
  }

  // Start animation if at least one canvas exists
  if (heroCanvas || footerCanvas) {
    animate();
  }
});
