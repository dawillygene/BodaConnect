import { useEffect, useRef } from 'react';

// Nairobi / East-Africa city centre as a reasonable default
const DEFAULT_CENTER = [-1.286389, 36.817223];
const DEFAULT_ZOOM = 13;

/**
 * Geocode a plain-text location string via the Nominatim OSM API.
 * Returns [lat, lng] or null on failure.
 */
async function geocode(location) {
  if (!location) {
    return null;
  }

  try {
    const url = `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(location)}&limit=1`;
    const response = await fetch(url, {
      headers: { 'Accept-Language': 'en' },
    });
    const results = await response.json();

    if (results.length === 0) {
      return null;
    }

    return [parseFloat(results[0].lat), parseFloat(results[0].lon)];
  } catch {
    return null;
  }
}

/**
 * RideMap – renders an interactive Leaflet map with optional pickup/destination markers.
 *
 * Props:
 *  - pickupLocation    {string}  Plain-text pickup address
 *  - destinationLocation {string} Plain-text destination address
 *  - height            {string}  CSS height (default '320px')
 *  - className         {string}  Extra CSS classes for the wrapper
 */
export function RideMap({ pickupLocation, destinationLocation, height = '320px', className = '' }) {
  const containerRef = useRef(null);
  const mapRef = useRef(null);
  const markersRef = useRef([]);

  // Initialise the map once the container is mounted
  useEffect(() => {
    // Dynamically import Leaflet to keep the initial bundle lean
    import('leaflet').then((L) => {
      if (!containerRef.current || mapRef.current) {
        return;
      }

      // Fix default icon paths that Vite breaks
      delete L.Icon.Default.prototype._getIconUrl;
      L.Icon.Default.mergeOptions({
        iconRetinaUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon-2x.png',
        iconUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png',
        shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
      });

      const map = L.map(containerRef.current, {
        center: DEFAULT_CENTER,
        zoom: DEFAULT_ZOOM,
        zoomControl: true,
        scrollWheelZoom: false,
        attributionControl: true,
      });

      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
        maxZoom: 19,
      }).addTo(map);

      mapRef.current = map;

      // Geocode and pin the locations
      plotMarkers(L, map, pickupLocation, destinationLocation);
    });

    return () => {
      if (mapRef.current) {
        mapRef.current.remove();
        mapRef.current = null;
      }
    };
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, []);

  // Re-plot markers when location strings change
  useEffect(() => {
    if (!mapRef.current) {
      return;
    }

    import('leaflet').then((L) => {
      plotMarkers(L, mapRef.current, pickupLocation, destinationLocation);
    });
  }, [pickupLocation, destinationLocation]);

  async function plotMarkers(L, map, pickup, destination) {
    // Clear existing markers
    markersRef.current.forEach((m) => m.remove());
    markersRef.current = [];

    const pickupIcon = L.divIcon({
      className: '',
      html: `<div style="width:14px;height:14px;border-radius:50%;background:#e57b1d;border:3px solid #fff;box-shadow:0 2px 8px rgba(0,0,0,.35)"></div>`,
      iconSize: [14, 14],
      iconAnchor: [7, 7],
    });

    const destinationIcon = L.divIcon({
      className: '',
      html: `<div style="width:14px;height:14px;border-radius:50%;background:#006e63;border:3px solid #fff;box-shadow:0 2px 8px rgba(0,0,0,.35)"></div>`,
      iconSize: [14, 14],
      iconAnchor: [7, 7],
    });

    const [pickupCoords, destCoords] = await Promise.all([
      geocode(pickup),
      geocode(destination),
    ]);

    const bounds = [];

    if (pickupCoords) {
      const marker = L.marker(pickupCoords, { icon: pickupIcon })
        .addTo(map)
        .bindPopup(`<strong>Pickup</strong><br/>${pickup ?? ''}`, { maxWidth: 200 });
      markersRef.current.push(marker);
      bounds.push(pickupCoords);
    }

    if (destCoords) {
      const marker = L.marker(destCoords, { icon: destinationIcon })
        .addTo(map)
        .bindPopup(`<strong>Destination</strong><br/>${destination ?? ''}`, { maxWidth: 200 });
      markersRef.current.push(marker);
      bounds.push(destCoords);
    }

    if (bounds.length === 2) {
      map.fitBounds(bounds, { padding: [40, 40], maxZoom: 15 });
    } else if (bounds.length === 1) {
      map.setView(bounds[0], DEFAULT_ZOOM);
    }
  }

  return (
    <div className={`ride-map-wrapper ${className}`} style={{ height }}>
      <link
        rel="stylesheet"
        href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        crossOrigin=""
      />
      <div ref={containerRef} style={{ width: '100%', height: '100%', borderRadius: 'inherit' }} />
    </div>
  );
}
