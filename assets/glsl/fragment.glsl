      precision highp float;
      precision highp int;

      uniform vec2 u_resolution;
      uniform float u_time;
      uniform vec2 u_mouse;  // Mouse position in pixels

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
          
          // Normalize mouse position to 0-1 range and adjust for aspect ratio
          vec2 mouse = u_mouse / u_resolution.xy;
          mouse.x *= aspect;
          
          vec3 backgroundColor = vec3(1.0);
          vec3 blobColor = vec3(0.94, 0.53, 0.15);
          
          vec3 color = backgroundColor;
          
          // Number of blobs to distribute using golden ratio spiral
          const int numBlobs = 24;
          
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
              
              // Calculate distance from blob to mouse
              float mouseDistance = length(blobPos - mouse);
              
              // Mouse interaction - blobs move away from mouse
              float mouseRepelStrength = 0.05 / (mouseDistance + 0.1);
              mouseRepelStrength = min(mouseRepelStrength, 0.15); // Limit maximum effect
              
              // Direction away from mouse
              vec2 repelDir = normalize(blobPos - mouse);
              
              // Apply repel effect with falloff based on distance
              blobPos += repelDir * mouseRepelStrength;
              
              // Get distance to blob center
              vec2 toBlob = uv - blobPos;
              float baseDist = length(toBlob);
              
              // Create distortion field
              float noiseScale = 6.0 + hash2(vec2(fi * 78.912, 34.56)).x * 1.5;
              float timeOffset = fi * 5.0;
              vec2 noisePos = toBlob * noiseScale + vec2(sin(u_time * 0.5 + timeOffset), cos(u_time * 0.5 + timeOffset));
              
              // Strong distortion for more amorphous shapes
              float distortion = fbm(noisePos, 1) * 0.5;
              
              // Mouse affects blob distortion - closer to mouse = more distortion
              float mouseDistortionEffect = 0.2 / (mouseDistance + 0.2);
              mouseDistortionEffect = min(mouseDistortionEffect, 0.5); // Limit maximum effect
              distortion += mouseDistortionEffect * 0.2 * sin(u_time * 2.0 + fi);
              
              // Vary blob size based on position - smaller near center, larger toward edges
              float blobSize = mix(0.01, 0.018, normalizedIndex);
              // Add slight randomness to size
              blobSize += hash2(vec2(fi * 23.456, 78.90)).x * 0.005;
              
              // Mouse affects blob size - closer to mouse = slightly larger
              float mouseSizeEffect = 0.1 / (mouseDistance + 0.5);
              mouseSizeEffect = min(mouseSizeEffect, 0.05); // Limit maximum effect
              blobSize += mouseSizeEffect * 0.01;
              
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