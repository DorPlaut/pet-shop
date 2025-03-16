/**
 * Refined Blob Background Animation
 * Creates evenly distributed, smooth organic blob shapes with improved interactions
 * Works with multiple SVG elements on the same page
 */
document.addEventListener('DOMContentLoaded', function () {
  // Make sure GSAP is loaded
  if (typeof gsap === 'undefined') {
    console.error('GSAP not loaded!');
    return;
  }

  // Get all SVG containers
  let svgs = document.querySelectorAll('.blob-background');
  console.log(svgs);

  if (svgs.length === 0) return;

  // Process each SVG independently
  svgs.forEach(function (svg) {
    initBlobsForSvg(svg);
  });

  function initBlobsForSvg(svg) {
    // Configuration
    const blobColor = '#ffe8d1';
    const isMobile = window.innerWidth < 768;
    const numBlobs = 10;

    // Create blobs for this specific SVG
    const blobs = [];

    // Get this SVG's dimensions
    const viewBox = svg.viewBox.baseVal || { width: 1200, height: 500 };
    const width = viewBox.width * 1.1;
    const height = viewBox.height;

    // Determine minimum distance between blobs
    const avgBlobSize = 100; // Average blob size
    const minDistance = isMobile ? avgBlobSize * 0.8 : avgBlobSize * 1.2;

    // Generate positions
    const positions = generateGridBasedPositions(numBlobs, width, height);

    // Create blobs at these positions
    for (let i = 0; i < positions.length; i++) {
      const position = positions[i];

      // Vary size based on position (smaller near center, larger toward edges)
      const distanceFromCenter = Math.sqrt(
        Math.pow((position.x - width / 2) / (width / 2), 2) +
          Math.pow((position.y - height / 2) / (height / 2), 2)
      );

      // Size increases slightly toward edges
      const size = avgBlobSize * (0.8 + distanceFromCenter * 0.4);

      // Complexity also varies slightly
      const complexity = isMobile
        ? 8 + Math.floor(Math.random() * 2) // 8-9 points on mobile
        : 10 + Math.floor(Math.random() * 3); // 10-12 points on desktop

      // Create blob path - create a NEW element for each SVG
      const blob = document.createElementNS(
        'http://www.w3.org/2000/svg',
        'path'
      );
      blob.setAttribute('fill', blobColor);
      blob.setAttribute('opacity', '0.8');

      // Generate initial path
      const initialPath = generateSmoothBlobPath(
        complexity,
        size,
        position.x,
        position.y
      );
      blob.setAttribute('d', initialPath);

      // Append to THIS svg only
      svg.appendChild(blob);

      // Store blob data
      blobs.push({
        element: blob,
        complexity: complexity,
        size: size,
        xPos: position.x,
        yPos: position.y,
        originalX: position.x,
        originalY: position.y,
        underMouseInfluence: false,
      });
    }

    // Animate blobs with GSAP
    blobs.forEach((blob, index) => {
      animateBlob(blob, index);
    });

    // Track mouse position for this SVG
    let mouseX = -1000;
    let mouseY = -1000;
    let mouseIsOver = false;

    // Set up observer for this SVG
    const observer = new IntersectionObserver(
      (entries) => {
        if (entries[0].isIntersecting) {
          // Start or resume animations
          gsap.globalTimeline.resume();
        } else {
          // Pause animations when not visible
          gsap.globalTimeline.pause();
        }
      },
      { threshold: 0.1 }
    );

    observer.observe(svg.parentElement || svg);

    // Mouse interaction for this SVG
    document.addEventListener('mousemove', function (e) {
      const rect = svg.getBoundingClientRect();
      mouseX = e.clientX - rect.left;
      mouseY = e.clientY - rect.top;

      mouseIsOver =
        mouseX >= 0 &&
        mouseX <= rect.width &&
        mouseY >= 0 &&
        mouseY <= rect.height;

      const svgX = (mouseX / rect.width) * width;
      const svgY = (mouseY / rect.height) * height;

      if (mouseIsOver) {
        applyMouseInfluence(svgX, svgY);
      }
    });

    document.addEventListener('mouseleave', function () {
      mouseIsOver = false;
      resetAllBlobPositions();
    });

    function animateBlob(blob, index) {
      // Create a new target path with small variations
      const targetPath = generateSmoothBlobPath(
        blob.complexity,
        blob.size * (0.9 + Math.random() * 0.2), // Small size variation (0.9-1.1)
        blob.xPos + (Math.random() * 20 - 10), // Small position shift (±10px)
        blob.yPos + (Math.random() * 20 - 10) // Small position shift (±10px)
      );

      // Animation duration between 20-30 seconds for smoother motion
      const duration = 50 + Math.random() * 10;

      // Animate the blob shape
      gsap.to(blob.element, {
        duration: duration,
        attr: { d: targetPath },
        ease: 'sine.inOut',
        onComplete: function () {
          // Only continue animation if not under mouse influence
          if (!blob.underMouseInfluence) {
            animateBlob(blob, index);
          }
        },
      });
    }

    function applyMouseInfluence(svgX, svgY) {
      // Only process if we have valid coordinates
      if (!mouseIsOver && (svgX < -100 || svgY < -100)) {
        resetAllBlobPositions();
        return;
      }

      blobs.forEach((blob) => {
        const dx = blob.xPos - svgX;
        const dy = blob.yPos - svgY;
        const distance = Math.sqrt(dx * dx + dy * dy);

        // Mouse influence range
        const influenceRange = 300;

        // Only affect blobs within range
        if (distance < influenceRange) {
          // If not already under mouse influence, pause the breathing animation
          if (!blob.underMouseInfluence && blob.breathingTimeline) {
            // Smoothly pause the breathing timeline
            gsap.to(blob.breathingTimeline, {
              timeScale: 0,
              duration: 0.5,
            });
          }

          blob.underMouseInfluence = true;

          // Calculate influence based on distance (stronger when closer)
          const influence = Math.max(
            0,
            (influenceRange - distance) / influenceRange
          );
          const angle = Math.atan2(dy, dx);
          const force = influence * 50; // Maximum displacement

          // Calculate target position
          const targetX = blob.originalX + Math.cos(angle) * force;
          const targetY = blob.originalY + Math.sin(angle) * force;

          // Generate new blob shape at the influenced position
          const newPath = generateSmoothBlobPath(
            blob.complexity,
            blob.size * (1 - influence * 0.1), // Slightly smaller when pushed
            targetX,
            targetY
          );

          // Animate to the new shape - faster when closer to mouse
          gsap.to(blob.element, {
            duration: 3 - influence * 0.5, // Faster when closer (0.3-0.8s)
            attr: { d: newPath },
            ease: 'power2.out',
          });
        } else if (blob.underMouseInfluence) {
          // If the blob was under influence but no longer is,
          // gradually return to normal animation
          blob.underMouseInfluence = false;
          resetBlobPosition(blob);
        }
      });
    }

    // Function to reset all blobs
    function resetAllBlobPositions() {
      blobs.forEach((blob) => {
        if (blob.underMouseInfluence) {
          resetBlobPosition(blob);
        }
      });
    }

    // Function to reset a single blob position
    function resetBlobPosition(blob) {
      if (!blob) return;

      // Generate path at original position
      const resetPath = generateSmoothBlobPath(
        blob.complexity,
        blob.size,
        blob.originalX,
        blob.originalY
      );

      // Animate back to original position
      gsap.to(blob.element, {
        duration: 5,
        attr: { d: resetPath },
        ease: 'elastic.out(0.5, 0.3)',
        onComplete: function () {
          // Resume breathing animation if it exists
          if (blob.breathingTimeline) {
            gsap.to(blob.breathingTimeline, {
              timeScale: 1,
              duration: 2,
            });
          }

          // Resume normal animation
          animateBlob(blob, blobs.indexOf(blob));
        },
      });
    }

    // Initialize the breathing animation for this SVG
    startBreathingAnimation();

    function startBreathingAnimation() {
      blobs.forEach((blob, index) => {
        // Create a timeline for this blob
        blob.breathingTimeline = gsap.timeline({
          repeat: -1,
          yoyo: true,
          paused: true, // Start paused, we'll control it manually
        });

        // Calculate unique timing offset for each blob to avoid synchronized movement
        const offset = (index / blobs.length) * Math.PI * 2;
        const delay = index * 0.3; // Stagger start times

        // Create 3 slightly different paths for this blob's breathing cycle
        const breathingPaths = [];
        for (let i = 0; i < 3; i++) {
          // Each path has slightly different characteristics
          const scaleVariation = 0.03 + i * 0.02; // 3-7% size variation
          const posVariation = 3 + i * 2; // 3-7px position variation

          // Create variations based on sine wave patterns for organic movement
          const sizeMultiplier =
            1 + Math.sin(offset + (i * Math.PI) / 3) * scaleVariation;
          const xOffset = Math.sin(offset + (i * Math.PI) / 4) * posVariation;
          const yOffset = Math.cos(offset + (i * Math.PI) / 5) * posVariation;

          breathingPaths.push(
            generateSmoothBlobPath(
              blob.complexity,
              blob.size * sizeMultiplier,
              blob.originalX + xOffset,
              blob.originalY + yOffset
            )
          );
        }

        // Add breathing stages to the timeline
        // Using different durations for more organic feel
        blob.breathingTimeline
          .to(blob.element, {
            duration: 4 + Math.random() * 4, // 8-12 seconds
            attr: { d: breathingPaths[0] },
            ease: 'sine.inOut',
          })
          .to(blob.element, {
            duration: 5 + Math.random() * 5, // 10-15 seconds
            attr: { d: breathingPaths[1] },
            ease: 'sine.inOut',
          })
          .to(blob.element, {
            duration: 4.5 + Math.random() * 4, // 9-13 seconds
            attr: { d: breathingPaths[2] },
            ease: 'sine.inOut',
          })
          .to(blob.element, {
            duration: 5.5 + Math.random() * 3, // 11-14 seconds
            attr: { d: breathingPaths[0] }, // Back to first path to complete the cycle
            ease: 'sine.inOut',
          });

        // Start the timeline with a random progress so all blobs don't breathe in sync
        blob.breathingTimeline.progress(Math.random());
        blob.breathingTimeline.play();
      });
    }
  }

  // Shared utility functions used by all SVGs
  function generateSmoothBlobPath(complexity, size, xPosition, yPosition) {
    // Create points around a circle with controlled randomness
    const points = [];
    const angleStep = (Math.PI * 2) / complexity;

    for (let i = 0; i < complexity; i++) {
      const angle = i * angleStep;
      // More controlled randomness (0.85 to 1.15 of size)
      const radius = size * (0.5 + Math.random() * 0.5);
      const x = xPosition + Math.cos(angle) * radius;
      const y = yPosition + Math.sin(angle) * radius;
      points.push({ x, y });
    }

    // Create SVG path with bezier curves for smoother shapes
    let pathData = '';

    // Use a more sophisticated approach with multiple control points
    for (let i = 0; i < points.length; i++) {
      const point = points[i];
      const prevPoint = points[(i - 1 + points.length) % points.length];
      const nextPoint = points[(i + 1) % points.length];

      if (i === 0) {
        pathData = `M ${point.x},${point.y}`;
      } else {
        // Calculate control points for smoother curves
        // Use tension factor to control the curve smoothness
        const tension = 0.33;

        // First control point - based on previous point and current point
        const cp1x = prevPoint.x + (point.x - prevPoint.x) * tension;
        const cp1y = prevPoint.y + (point.y - prevPoint.y) * tension;

        // Second control point - based on current point and next point
        const cp2x = point.x - (nextPoint.x - prevPoint.x) * tension;
        const cp2y = point.y - (nextPoint.y - prevPoint.y) * tension;

        pathData += ` C ${cp1x},${cp1y} ${cp2x},${cp2y} ${point.x},${point.y}`;
      }
    }

    // Close the path with a smooth curve back to the first point
    const firstPoint = points[0];
    const lastPoint = points[points.length - 1];
    const secondPoint = points[1];

    const tension = 0.33;
    const cp1x = lastPoint.x + (firstPoint.x - lastPoint.x) * tension;
    const cp1y = lastPoint.y + (firstPoint.y - lastPoint.y) * tension;
    const cp2x = firstPoint.x - (secondPoint.x - lastPoint.x) * tension;
    const cp2y = firstPoint.y - (secondPoint.y - lastPoint.y) * tension;

    pathData += ` C ${cp1x},${cp1y} ${cp2x},${cp2y} ${firstPoint.x},${firstPoint.y}`;
    pathData += ' Z';

    return pathData;
  }

  function generateGridBasedPositions(numPositions, width, height) {
    // Calculate optimal grid dimensions based on aspect ratio
    const aspectRatio = width / height;

    // Calculate rows and columns based on the aspect ratio
    // This ensures proper distribution across the entire area
    let cols = Math.round(Math.sqrt(numPositions * aspectRatio));
    let rows = Math.round(Math.sqrt(numPositions / aspectRatio));

    // Ensure we have enough cells
    while (rows * cols < numPositions) {
      if (width / cols > height / rows) {
        cols++;
      } else {
        rows++;
      }
    }

    // Calculate cell size
    const cellWidth = width / cols;
    const cellHeight = height / rows;

    // Define blob size based on cell size (with some margin)
    const blobSize = Math.min(cellWidth, cellHeight) * 0.7;

    // Create an array to hold all possible positions
    const allPositions = [];

    // Generate positions for each cell in the grid
    for (let row = 0; row < rows; row++) {
      for (let col = 0; col < cols; col++) {
        // Add slight jitter within each cell to avoid perfect grid alignment
        const jitterX = (Math.random() * 0.4 - 0.2) * cellWidth;
        const jitterY = (Math.random() * 0.4 - 0.2) * cellHeight;

        // Calculate the center position of this cell
        const x = (col + 0.5) * cellWidth + jitterX - 50;
        const y = (row + 0.5) * cellHeight + jitterY;

        // Add position to our array
        allPositions.push({
          x: x,
          y: y,
          size: blobSize * (0.8 + Math.random() * 0.2), // Small size variation
        });
      }
    }

    // If we generated more positions than needed, select the ones
    // that provide the best coverage
    if (allPositions.length > numPositions) {
      // Mark each position with a priority score
      // Higher score = higher priority to keep
      allPositions.forEach((pos, index) => {
        // Calculate normalized position (0-1 range)
        const normX = pos.x / width;
        const normY = pos.y / height;

        // Distance from center (0-0.5 range)
        const distFromCenterX = Math.abs(normX - 0.5);
        const distFromCenterY = Math.abs(normY - 0.5);

        // Prioritize positions near edges and corners
        // Max value will be close to 1 for corners, lower for center
        pos.priority = distFromCenterX + distFromCenterY;

        // Store original index for stable sort
        pos.index = index;
      });

      // Sort by priority (highest first)
      allPositions.sort((a, b) => {
        // Primary sort by priority
        const priorityDiff = b.priority - a.priority;

        // If priorities are close, use original index for stable sort
        if (Math.abs(priorityDiff) < 0.1) {
          return a.index - b.index;
        }

        return priorityDiff;
      });

      // Take the top positions by priority
      return allPositions.slice(0, numPositions);
    }

    return allPositions;
  }

  // Handle window resize
  window.addEventListener('resize', function () {
    const wasAlreadyMobile = window.innerWidth < 768;
    const isNowMobile = window.innerWidth < 768;

    // Only regenerate blobs if mobile status changed
    if (wasAlreadyMobile !== isNowMobile) {
      location.reload();
    }
  });
});
